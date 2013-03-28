<?php
namespace Acme\Bundle\DemoSegmentationTreeBundle\Helper;

/**
 * Helper for Tree Controller to format segments in JSON content
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2012 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 */
class JsonSegmentHelper
{
    /**
     * Format content for node creation response
     *
     * @param integer $status    Response status value
     * @param integer $segmentId Segment id
     *
     * @return array
     * @static
     */
    public static function createNodeResponse($status, $segmentId)
    {
        return array('status' => $status, 'id' => $segmentId);
    }

    /**
     * Format in array content segment for JSON response
     *
     * @param ArrayCollection $segments
     *
     * @return array
     * @static
     */
    public static function childrenResponse($segments)
    {
        $return = array();

        foreach ($segments as $segment) {
            $return[] = array(
                    'attr' => array('id' => 'node_'. $segment->getId(), 'rel' => 'folder'),
                    'data' => $segment->getTitle(),
                    'state'=> 'closed'
            );
        }

        return $return;
    }

    /**
     * Format trees content for response
     *
     * @param ArrayCollection $segments
     *
     * @return array
     * @static
     */
    public static function treesResponse($segments)
    {
        $return = array();

        foreach ($segments as $segment) {
            $return[] = array('id' => $segment->getId(), 'title' => $segment->getTitle());
        }

        return $return;
    }


    /**
     * Format in array content for JSON search response
     * @param ArrayCollection $segments
     *
     * @return array
     * @static
     */
    public static function searchResponse($segments)
    {
        $return = array();

        foreach ($segments as $segment) {
            $return[] = '#node_'. $segment->getId();
        }

        return $return;
    }

    /**
     * Return a status OK
     * @return array
     * @static
     */
    public static function statusOKResponse()
    {
        return array('status' => 1);
    }

    /**
     * Format product list 
     * @param ArrayCollection $products
     *
     * @return array
     * @static
     */
    public static function ProductsResponse($products)
    {
        $return = array();

        foreach ($products as $product) {
            $return[] = array(
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'description' => $product->getDescription()
            );
        }

        return $return;
    }
}
