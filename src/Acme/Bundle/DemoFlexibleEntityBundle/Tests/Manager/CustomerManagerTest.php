<?php
namespace Acme\Bundle\DemoFlexibleEntityBundle\Test\Manager;

use Oro\Bundle\FlexibleEntityBundle\Model\AttributeType\OptionSimpleRadioType;

use Oro\Bundle\FlexibleEntityBundle\Model\AttributeType\DateType;

use Oro\Bundle\FlexibleEntityBundle\Model\AttributeType\TextType;

use Oro\Bundle\FlexibleEntityBundle\Entity\AttributeOption;

use Acme\Bundle\DemoFlexibleEntityBundle\Entity\CustomerValue;

use Oro\Bundle\FlexibleEntityBundle\Model\AbstractAttributeType;

use Acme\Bundle\DemoFlexibleEntityBundle\Entity\Customer;

use Acme\Bundle\DemoFlexibleEntityBundle\Tests\KernelAwareTest;

/**
 * Test related class
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2012 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 *
 */
class CustomerManagerTest extends KernelAwareTest
{

    /**
     * @var FlexibleManager
     */
    protected $manager;

    /**
     * @staticvar integer
     */
    protected static $customerCount = 0;

    /**
     * List ofnom  customers
     * @var multitype
     */
    protected $customerList = array();

    /**
     * @var EntityAttributeValue
     */
    protected $attCompany;

    /**
     * @var EntityAttributeValue
     */
    protected $attDob;

    /**
     * @var EntityAttributeValue
     */
    protected $attGender;

    /**
     * Option gender
     * @var AttributeOption
     */
    protected $option;

    /**
     * UT set up
     */
    public function setUp()
    {
        parent::setUp();
        $this->manager = $this->container->get('customer_manager');

        // create attributes
        $this->attCompany = $this->createAttribute(
            'company',
            'Company',
            new TextType()
        );
        $this->attDob = $this->createAttribute(
            'dob',
            'Date of Birth',
            new DateType()
        );
        $this->attGender = $this->createAttribute(
            'gender',
            'Gender',
            new OptionSimpleRadioType(),
            array('Mr', 'Mrs')
        );


        // create entities
        for ($idx=0; $idx<5; $idx++) {
            $this->customerList[$idx] = $this->createCustomer('Nicolas', 'Dupont', 'Akeneo', '2012-12-25', $this->option);
        }
        $this->customerList[$idx++] = $this->createCustomer('Romain', 'Monceau');
        $this->customerList[$idx++] = $this->createCustomer('Romain', 'Dupont', 'Akeneo');


        // commit add transaction
        $this->manager->getStorageManager()->flush();
    }

    /**
     * UT tear down
     */
    public function tearDown()
    {
        // remove entities
        foreach ($this->customerList as $customer) {
            $this->manager->getStorageManager()->remove($customer);
        }
        $this->customerList = array();

        // remove attributes
        $this->manager->getStorageManager()->remove($this->attCompany);
        $this->manager->getStorageManager()->remove($this->attDob);
        $this->manager->getStorageManager()->remove($this->attGender);

        // commit remove transaction
        $this->manager->getStorageManager()->flush();
        parent::tearDown();
    }

    /**
     * Create customer
     * @param string $firstname Firstname of the customer
     * @param string $lastname  Lastname of the customer
     * @param string $company   Company of the customer
     * @param string $dob       Date of Birth of the customer
     * @param string $gender    Gender of the customer
     *
     * @return Customer
     */
    protected function createCustomer($firstname = "", $lastname = "", $company = "", $dob = "", $gender = null)
    {
        // create values
        $valueCompany = $this->createFlexibleValue($this->attCompany, $company);
        $valueDob     = $this->createFlexibleValue($this->attDob, new \DateTime($dob));
        if ($gender !== null) {
            $valueGender  = $this->createFlexibleValue($this->attGender, $gender);
        }

        // create customer
        $customer = $this->manager->createFlexible();
        $customer->setFirstname($firstname);
        $customer->setLastname($lastname);
        $customer->setEmail('email-'.$firstname.'.'.$lastname.self::$customerCount++.'@mail.com');

        // add values
        $customer->addValue($valueCompany);
        $customer->addValue($valueDob);
        if (isset($valueGender)) {
            $customer->addValue($valueGender);
        }

        // persists customer
        $this->manager->getStorageManager()->persist($customer);

        return $customer;
    }

    /**
     * Create value
     * @param Attribute $attribute Attribute object
     * @param mixed     $value     Value of the attribute
     *
     * @return CustomerValue
     */
    protected function createFlexibleValue($attribute, $value)
    {
        // create value
        $entityValue = $this->manager->createFlexibleValue();
        $entityValue->setAttribute($attribute);
        if ($attribute->getCode() == 'gender') {
            $entityValue->setOption($value);
        } else {
            $entityValue->setData($value);
        }

        return $entityValue;
    }

    /**
     * Create attribute
     *
     * @param string    $code          Attribute code
     * @param string    $title         Attribute title
     * @param string    $attributeType Attribute type
     * @param multitype $options       Options list
     *
     * @return Attribute
     */
    protected function createAttribute($code, $title, $attributeType, $options = array())
    {
        // create attribute
        $attribute = $this->manager->createAttribute($attributeType);
        $attribute->setCode($code);

        // create options
        foreach ($options as $option) {
            $this->option = $this->manager->createAttributeOption();
            $optVal = $this->manager->createAttributeOptionValue();
            $optVal->setValue($option);
            $this->option->addOptionValue($optVal);
            $attribute->addOption($this->option);
        }

        // persists attribute
        $this->manager->getStorageManager()->persist($attribute);

        return $attribute;
    }

    /**
     * Test related method
     */
    public function testCreateEntity()
    {
        $newCustomer = $this->manager->createFlexible();
        $this->assertTrue($newCustomer instanceof Customer);
        $newCustomer->setFirstname('Nicolas');
        $newCustomer->setLastname('Dupont');
        $this->assertEquals($newCustomer->getFirstname(), 'Nicolas');
    }

    /**
     * Test find by with attributes method
     */
    public function testQueryFindByWithAttributes()
    {
        // test find by with attributes
        $customers = $this->getRepo()->findByWithAttributes();
        $this->assertCount(7, $customers);

        // test with lazy loading
        $customers = $this->getRepo()->findBy(array());
        $this->assertCount(7, $customers);

        // test filtering by firstname
        $customers = $this->getRepo()->findByWithAttributes(array(), array('firstname' => 'Nicolas'));
        $this->assertCount(5, $customers);
        $customers = $this->getRepo()->findBy(array('firstname' => 'Nicolas'));
        $this->assertCount(5, $customers);

        // test filtering by firstname and company
        $customers = $this->getRepo()->findByWithAttributes(array('company'), array('firstname' => 'Romain', 'company' => 'Akeneo'));
        $this->assertCount(1, $customers);

        // test filtering and limiting
        $customers = $this->getRepo()->findByWithAttributes(array('company'), array('lastname' => 'Dupont', 'company' => 'Akeneo'), null, 5, 0);
        $this->assertCount(6, $customers);

        // test filtering, limiting and ordering
        $customers = $this->getRepo()->findByWithAttributes(array(), array('firstname' => 'Romain'), array('lastname' => 'ASC'), 5, 0);
        $this->assertCount(2, $customers);
    }

    /**
     * @return Oro\Bundle\FlexibleEntityBundle\Entity\Repository\FlexibleEntityRepository
     */
    protected function getRepo()
    {
        return $this->manager->getFlexibleRepository();
    }
}
