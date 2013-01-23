<?php

namespace Acme\Bundle\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use JMS\SecurityExtraBundle\Annotation\Secure;

use Oro\Bundle\FlexibleEntityBundle\Model\AbstractAttributeType;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\UserBundle\Form\Type\UserType;

/**
 * @Route("/user")
 */
class UserController extends Controller
{
   /**
    * @Route("/show/{id}", name="acme_demo_user_show", requirements={"id"="\d+"})
    * @Template("AcmeDemoBundle:User:edit.html.twig")
    */
    public function showAction(User $user)
    {
        return $this->editAction($user);
    }

   /**
    * Create user form
    *
    * @Route("/create", name="acme_demo_user_create")
    * @Template("AcmeDemoBundle:User:edit.html.twig")
    */
    public function createAction()
    {
        return $this->editAction(new User());
    }

   /**
    * Edit user form
    *
    * @Route("/edit/{id}", name="acme_demo_user_edit", requirements={"id"="\d+"}, defaults={"id"=0})
    * @Template
    */
    public function editAction(User $entity)
    {
        $request = $this->getRequest();
        $form    = $this->createForm('oro_user_form', $entity);

        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $fm = $this->get('oro_user.flexible_manager');

                /**
                 * Process all entity attributes manually
                 *
                 * @todo move this code to FlexibleEntityBundle
                 */
                foreach ($fm->getEntityRepository()->getCodeToAttributes([]) as $attr) {
                    $field = $form->get($attr->getCode());

                    if ($field) {
                        $data = $field->getData();

                        if ($attr->getBackendType() == AbstractAttributeType::BACKEND_TYPE_OPTION) {
                            /**
                             * @todo how to handle options?
                             */
                        } else {
                            if (!$entity->getId() || !($value = $entity->getValue($attr->getCode()))) {
                                // add new value
                                $value = $fm->createEntityValue()->setAttribute($attr);

                                $entity->addValue($value);
                            }

                            $value->setData($data);
                        }
                    }
                }

                /**
                 * @todo maybe it is better to use fos_user.user_manager or
                 *       oro_user.user_manager to manipulate user data?
                 */
                $fm->getStorageManager()->persist($entity);
                $fm->getStorageManager()->flush();

                $this->get('session')->getFlashBag()->add('success', 'User successfully saved');

                return $this->redirect($this->generateUrl('acme_demo_user_index'));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }

   /**
    * @Route("/remove/{id}", name="acme_demo_user_remove", requirements={"id"="\d+"})
    * @Template
    */
    public function removeAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($user);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'User successfully removed');

        return $this->redirect($this->generateUrl('acme_demo_user_index'));
    }

    /**
     * @Route("/{page}/{limit}", name="acme_demo_user_index", requirements={"page"="\d+","limit"="\d+"}, defaults={"page"=1,"limit"=20})
     * @Template
     */
    public function indexAction($page, $limit)
    {
        $query = $this
            ->getDoctrine()
            ->getEntityManager()
            ->createQuery('SELECT u FROM OroUserBundle:User u');

        return array(
            'pager'  => $this->get('knp_paginator')->paginate($query, $page, $limit),
        );
    }
}