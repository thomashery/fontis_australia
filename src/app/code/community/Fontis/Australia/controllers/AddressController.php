<?php
/**
 * Fontis Australia Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Fontis
 * @package    Fontis_Australia
 * @author     Thai Phan
 * @copyright  Copyright (c) 2014 Fontis Pty. Ltd. (http://www.fontis.com.au)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Controller for address validation requests
 *
 * @category   Fontis
 * @package    Fontis_Australia
 */
class Fontis_Australia_AddressController extends Mage_Core_Controller_Front_Action
{
    /**
     * Sends a request to the address validation backend to validate the address
     * the customer provided on the checkout page.
     */
    public function validateAction()
    {
        // Check for a valid POST request
        if (!$this->getRequest()->isPost()) {
            // Return a "405 Method Not Allowed" response
            $this->getResponse()->setHttpResponseCode(405);
            return;
        }

        $data = $this->getRequest()->getPost();

        // Check that all of the required fields are present
        if (empty($data) || !isset($data['country_id'], $data['region_id'], $data['street'], $data['city'], $data['postcode'])) {
            // Return a "400 Bad Request" response
            $this->getResponse()->setHttpResponseCode(400);
            return;
        }

        $country = Mage::getModel('directory/country')->load($data['country_id'])->getName();
        $region = Mage::getModel('directory/region')->load($data['region_id'])->getCode();

        $result = Mage::helper('australia/address')->validate(
            $data['street'],
            $region,
            $data['city'],
            $data['postcode'],
            $country
        );

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}
