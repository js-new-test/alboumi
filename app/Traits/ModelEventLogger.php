<?php
/**
 * *************************
 * Created by PhpStorm.
 * User: Mayur Tagadiya
 * Date: 8/8/2015
 * Time: 11:20 AM
 ***************************
 * Modified: Brijesh Khatri
 * StartDate: 12Oct2015
 * *************************
 */
namespace App\DB\Traits;

use App\DB\Activity;
use App\DB\Seller\Seller;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ModelEventLogger
 * @package App\DB\Traits
 *
 *  Automatically Log Add, Update, Delete events of Model.
 */
trait ModelEventLogger
{
    /**
     * Automatically boot with Model, and register Events handler.
     */
    protected static function bootModelEventLogger()
    {
        foreach (static::getRecordActivityEvents() as $eventName) {
            static::$eventName(function (Model $model) use ($eventName) {
                try {
                    $isDirty = $model->isDirty() ? $model->getDirty() : false;
                    $details = array();
                    $tempDetails = array();
                    if ($isDirty || $eventName == 'deleted') {
                        $getDirty = $model->getDirty();
                        $original = $model->getOriginal();
                        $results = $model->syncOriginal()->toArray();
                        $reflect = new \ReflectionClass($model);
                        switch ($reflect->getShortName()) {
                            case 'Seller':
                                //Show in brackets for seller history(To know which seller is this).
                                $tempDetails['firstname'] = $results['firstname'];
                                $tempDetails['lastname'] = $results['lastname'];
                                $tempDetails['parent_id'] = $results['parent_id'];
                                if ($tempDetails['parent_id'] != 0) {
                                    $seller = Seller::where('id', '=', $tempDetails['parent_id'])->first()->toArray();
                                    $details['parent_seller'] = $seller['firstname'] . ' ' . $seller['lastname'];
                                }
                                //Show in brackets for seller history(To know which seller is this).
                                switch ($eventName) {
                                    case 'created':
                                        foreach ($getDirty as $key => $value) {
                                            if (in_array($key, array('firstname', 'lastname', 'email', 'phone'))) {
                                                $details[$key] = $value;
                                            }
                                        }
                                        break;
                                    case 'updated':
                                        foreach ($getDirty as $key => $value) {
                                            $details[$key] = 'Changed from `' . $original[$key] . '` to `' . $value . '`';
                                        }
                                        break;
                                    case 'deleted':
                                        //$details = static::filterEventDetailForDeleted($model);
                                        $details['firstname'] = $results['firstname'];
                                        $details['lastname'] = $results['lastname'];
                                        $details['email'] = $results['email'];
                                        $details['phone'] = $results['phone'];
                                        break;
                                    default:
                                        echo 'No event found.';
                                }
                                return static::saveSellerActivities($reflect, $model, $eventName, $details, $tempDetails);
                                break;
                            case 'Store':
                                //Show in brackets for seller history(To know which seller is this).
                                $tempDetails['store_name'] = $results['store_name'];
                                //Show in brackets for seller history(To know which seller is this).
                                if (isset($results['user_id'])) {
                                    $seller = Seller::where('id', '=', $results['user_id'])->first()->toArray();
                                    $details['seller'] = $seller['firstname'] . ' ' . $seller['lastname'];
                                    $details['seller_unique_id'] = $results['seller_unique_id'];
                                }
                                switch ($eventName) {
                                    case 'created': //This case(created) maybe need to remove.
                                        foreach ($getDirty as $key => $value) {
                                            if (in_array($key, array('company_name', 'store_name'))) {
                                                $details[$key] = $value;
                                            }
                                        }
                                        break;
                                    case 'updated':
                                        foreach ($getDirty as $key => $value) {
                                            $details[$key] = 'Changed from `' . $original[$key] . '` to `' . $value . '`';
                                        }
                                        break;
                                    case 'deleted': //This case(created) maybe need to remove.
                                        $details['company_name'] = $results['company_name'];
                                        $details['store_name'] = $results['store_name'];
                                        break;
                                    default:
                                        echo 'No event found.';
                                }
                                return static::saveSellerStoreActivities($reflect, $model, $eventName, $details, $tempDetails);
                                break;
                            case 'Address':
                                //Show in brackets for seller history(To know which seller is this).
                                if (isset($results['user_id'])) {
                                    $seller = Seller::where('id', '=', $results['user_id'])->first()->toArray();
                                    $details['seller'] = $seller['firstname'] . ' ' . $seller['lastname'];
                                }
                                //Show in brackets for seller history(To know which seller is this).
                                switch ($eventName) {
                                    case 'created': //This case(created) maybe need to remove.
                                        foreach ($getDirty as $key => $value) {
                                            if (in_array($key, array('address_line_1', 'address_line_2', 'city', 'pincode'))) {
                                                $details[$key] = $value;
                                            }
                                        }
                                        break;
                                    case 'updated':
                                        foreach ($getDirty as $key => $value) {
                                            $details[$key] = 'Changed from `' . $original[$key] . '` to `' . $value . '`';
                                        }
                                        break;
                                    case 'deleted': //This case(created) maybe need to remove.
                                        $details['address_line_1'] = $results['address_line_1'];
                                        $details['address_line_2'] = $results['address_line_2'];
                                        $details['city'] = $results['city'];
                                        $details['pincode'] = $results['pincode'];
                                        break;
                                    default:
                                        echo 'No event found.';
                                }
                                return static::saveSellerExtraDetailActivities($reflect, $model, $eventName, $details);
                                break;
                            case 'BankDetail':
                                //Show in brackets for seller history(To know which seller is this).
                                $tempDetails['bank_name'] = $results['bank_name'];
                                if (isset($results['bank_mapping_id'])) {
                                    $seller = Seller::where('id', '=', $results['bank_mapping_id'])->first()->toArray();
                                    $details['seller'] = $seller['firstname'] . ' ' . $seller['lastname'];
                                }
                                //Show in brackets for seller history(To know which seller is this).
                                switch ($eventName) {
                                    case 'created': //This case(created) maybe need to remove.
                                        foreach ($getDirty as $key => $value) {
                                            if (in_array($key, array('beneficiary_name', 'bank_name', 'branch_name', 'bank_account_number'))) {
                                                $details[$key] = $value;
                                            }
                                        }
                                        break;
                                    case 'updated':
                                        foreach ($getDirty as $key => $value) {
                                            $details[$key] = 'Changed from `' . $original[$key] . '` to `' . $value . '`';
                                        }
                                        break;
                                    case 'deleted': //This case(created) maybe need to remove.
                                        $details['beneficiary_name'] = $results['beneficiary_name'];
                                        $details['bank_name'] = $results['bank_name'];
                                        $details['branch_name'] = $results['branch_name'];
                                        $details['bank_account_number'] = $results['bank_account_number'];
                                        break;
                                    default:
                                        echo 'No event found.';
                                }
                                return static::saveSellerBankDetailActivities($reflect, $model, $eventName, $details, $tempDetails);
                                break;
                            case 'LogisticDetail':
                                //Show in brackets for seller history(To know which seller is this).
                                if (isset($results['user_id'])) {
                                    $seller = Seller::where('id', '=', $results['user_id'])->first()->toArray();
                                    $details['seller'] = $seller['firstname'] . ' ' . $seller['lastname'];
                                }
                                //Show in brackets for seller history(To know which seller is this).
                                switch ($eventName) {
                                    case 'created': //This case(created) maybe need to remove.
                                        foreach ($getDirty as $key => $value) {
                                            if (in_array($key, array('logistic_used', 'contact_person_name', 'address_line_1', 'city', 'pincode', 'phone1'))) {
                                                $details[$key] = $value;
                                            }
                                        }
                                        break;
                                    case 'updated':
                                        foreach ($getDirty as $key => $value) {
                                            $details[$key] = 'Changed from `' . $original[$key] . '` to `' . $value . '`';
                                        }
                                        break;
                                    case 'deleted': //This case(created) maybe need to remove.
                                        $details['logistic_used'] = $results['logistic_used'];
                                        $details['contact_person_name'] = $results['contact_person_name'];
                                        $details['address_line_1'] = $results['address_line_1'];
                                        $details['city'] = $results['city'];
                                        $details['pincode'] = $results['pincode'];
                                        $details['phone1'] = $results['phone1'];
                                        break;
                                    default:
                                        echo 'No event found.';
                                }
                                return static::saveSellerExtraDetailActivities($reflect, $model, $eventName, $details);
                                break;
                            case 'Document':
                                //Show in brackets for seller history(To know which seller is this).
                                if (isset($results['user_id']) && $results['user_id'] != '') {
                                    $seller = Seller::where('id', '=', $results['user_id'])->first()->toArray();
                                    $details['seller'] = $seller['firstname'] . ' ' . $seller['lastname'];
                                }
                                //Show in brackets for seller history(To know which seller is this).
                                switch ($eventName) {
                                    case 'created': //This case(created) maybe need to remove.
                                        foreach ($getDirty as $key => $value) {
                                            if (in_array($key, array('details'))) {
                                                $details[$key] = $value;
                                            }
                                        }
                                        break;
                                    case 'updated':
                                        foreach ($getDirty as $key => $value) {
                                            $details[$key] = 'Changed from `' . $original[$key] . '` to `' . $value . '`';
                                        }
                                        break;
                                    case 'deleted': //This case(delete) maybe need to remove.
                                        $details['details'] = $results['details'];
                                        break;
                                    default:
                                        echo 'No event found.';
                                }
                                return static::saveSellerExtraDetailActivities($reflect, $model, $eventName, $details);
                                break;
                            case 'Product':
                                //Show in brackets for product history(To know which product is this).
                                $tempDetails['title'] = $results['title'];
                                //Show in brackets for product history(To know which product is this).
                                switch ($eventName) {
                                    case 'created':
                                        foreach ($getDirty as $key => $value) {
                                            if (in_array($key, array('product_type', 'title', 'subtitle', 'description', 'bf_sku', 'startdate', 'enddate'))) {
                                                $details[$key] = $value;
                                            }
                                        }
                                        break;
                                    case 'updated':
                                        foreach ($getDirty as $key => $value) {
                                            $details[$key] = 'Changed from `' . $original[$key] . '` to `' . $value . '`';
                                        }
                                        break;
                                    case 'deleted':
                                        $details['product_type'] = $results['product_type'];
                                        $details['title'] = $results['title'];
                                        $details['subtitle'] = $results['subtitle'];
                                        $details['description'] = $results['description'];
                                        $details['bf_sku'] = $results['bf_sku'];
                                        $details['startdate'] = $results['startdate'];
                                        $details['enddate'] = $results['enddate'];
                                        break;
                                    default:
                                        echo 'No event found.';
                                }
                                return static::saveProductActivities($reflect, $model, $eventName, $details, $tempDetails);
                                break;
                            case 'Cmspage':
                                //Show in brackets for product history(To know which product is this).
                                $tempDetails['title'] = $results['title'];
                                //Show in brackets for product history(To know which product is this).
                                switch ($eventName) {
                                    case 'created':
                                        $details = $getDirty;
                                        break;
                                    case 'updated':
                                        foreach ($getDirty as $key => $value) {
                                            $details[$key] = 'Changed from `' . $original[$key] . '` to `' . $value . '`';
                                        }
                                        break;
                                    case 'deleted':
                                        $details['title'] = $results['title'];
                                        break;
                                    default:
                                        echo 'No event found.';
                                }
                                return static::saveCmsActivities($reflect, $model, $eventName, $details, $tempDetails);
                                break;
                            case 'CustomerBankDetail':
                                $tempDetails['holder_name'] = $results['holder_name'];
                                switch ($eventName) {
                                    case 'updated':
                                        foreach ($getDirty as $key => $value) {
                                            $title = str_replace('_', ' ', $key);
                                            $details[$key] = 'Changed from `' . $original[$key] . '` to `' . $value . '`';
                                        }
                                        break;
                                    default:
                                        //echo 'No event found.';
                                }
                                return static::saveCustomerBankDetailsActivities($reflect, $model, $eventName, $details, $tempDetails);
                            default:
                                //echo 'No activities found.';
                        }
                    }
                } catch (\Exception $e) {
                    return true;
                }
            });
        }
    }

    /**
     * Brijesh Khatri - 13Oct2015
     * This function is used to save sellers related activities details.
     * @param $reflect
     * @param $model
     * @param $eventName
     * @param $details
     * @param $tempDetails
     */
    protected static function saveSellerActivities($reflect, $model, $eventName, $details, $tempDetails)
    {
        //Checking for users is seller or sub seller.
        if($tempDetails['parent_id'] == 0) {
            $getShortName = $reflect->getShortName();
        } else {
            $getShortName = 'Sub seller';
        }

        Activity::log([
            'contentId' => $model->id,
            'contentType' => get_class($model),
            'action' => static::getActionName($eventName),
            'description' => $getShortName . '(' . $tempDetails['firstname'] . ' ' . $tempDetails['lastname'] . ') has been ' . $eventName,
            'details' => json_encode($details)
        ]);
    }

    /**
     * Brijesh Khatri - 16Oct2015
     * This function is used to save seller store related activities details.
     * @param $reflect
     * @param $model
     * @param $eventName
     * @param $details
     * @param $tempDetails
     */
    protected static function saveSellerStoreActivities($reflect, $model, $eventName, $details, $tempDetails)
    {
        Activity::log([
            'contentId' => $model->id,
            'contentType' => get_class($model),
            'action' => static::getActionName($eventName),
            'description' => $reflect->getShortName() . '(' . $tempDetails['store_name'] . ') has been ' . $eventName,
            'details' => json_encode($details)
        ]);
    }

    /**
     * Brijesh Khatri - 16Oct2015
     * This function is used to save seller bank detail related activities details.
     * @param $reflect
     * @param $model
     * @param $eventName
     * @param $details
     * @param $tempDetails
     */
    protected static function saveSellerBankDetailActivities($reflect, $model, $eventName, $details, $tempDetails)
    {
        Activity::log([
            'contentId' => $model->id,
            'contentType' => get_class($model),
            'action' => static::getActionName($eventName),
            'description' => $reflect->getShortName() . '(' . $tempDetails['bank_name'] . ') has been ' . $eventName,
            'details' => json_encode($details)
        ]);
    }

    /**
     * Brijesh Khatri - 16Oct2015
     * This function is used to save seller address, logistic and document related activities details.
     * @param $reflect
     * @param $model
     * @param $eventName
     * @param $details
     */
    protected static function saveSellerExtraDetailActivities($reflect, $model, $eventName, $details)
    {
        Activity::log([
            'contentId' => $model->id,
            'contentType' => get_class($model),
            'action' => static::getActionName($eventName),
            'description' => $reflect->getShortName() . ' has been ' . $eventName,
            'details' => json_encode($details)
        ]);
    }

    /**
     * Brijesh Khatri - 13Oct2015
     * This function is used to save products related activities details.
     * @param $reflect
     * @param $model
     * @param $eventName
     * @param $details
     * @param $tempDetails
     */
    protected static function saveProductActivities($reflect, $model, $eventName, $details, $tempDetails)
    {
        Activity::log([
            'contentId' => $model->id,
            'contentType' => get_class($model),
            'action' => static::getActionName($eventName),
            'description' => $reflect->getShortName() . '(' . $tempDetails['title'] . ') has been ' . $eventName,
            'details' => json_encode($details)
        ]);
    }

    /**
     * Brijesh Khatri - 16Oct2015
     * This function is used to save CMS related activities details.
     * @param $reflect
     * @param $model
     * @param $eventName
     * @param $details
     * @param $tempDetails
     */
    protected static function saveCmsActivities($reflect, $model, $eventName, $details, $tempDetails)
    {
        Activity::log([
            'contentId' => $model->id,
            'contentType' => get_class($model),
            'action' => static::getActionName($eventName),
            'description' => $reflect->getShortName() . '(' . $tempDetails['title'] . ') has been ' . $eventName,
            'details' => json_encode($details)
        ]);
    }

    /**
     * Brijesh Khatri - 30Nov2015
     * This function is used to save customer bank details activities details.
     * @param $reflect
     * @param $model
     * @param $eventName
     * @param $details
     * @param $tempDetails
     */
    protected static function saveCustomerBankDetailsActivities($reflect, $model, $eventName, $details, $tempDetails)
    {
        if($tempDetails['holder_name'] != '') {
            $description = $reflect->getShortName() . ' has been ' . $eventName . ' by '. $tempDetails['holder_name'];
        } else {
            $description = $reflect->getShortName() . ' has been ' . $eventName;
        }

        Activity::log([
            'contentId' => $model->id,
            'contentType' => get_class($model),
            'action' => static::getActionName($eventName),
            'description' => $description,
            'details' => json_encode($details)
        ]);
    }

    /**
     * Set the default events to be recorded if the $recordEvents
     * property does not exist on the model.
     *
     * @return array
     */
    protected static function getRecordActivityEvents()
    {
        if (isset(static::$recordEvents)) {
            return static::$recordEvents;
        }

        return ['created', 'updated', 'deleted'];
    }

    /**
     * Return Suitable action name for Supplied Event
     *
     * @param $event
     * @return string
     */
    protected static function getActionName($event)
    {
        switch (strtolower($event)) {
            case 'created':
                return 'create';
                break;
            case 'updated':
                return 'update';
                break;
            case 'deleted':
                return 'delete';
                break;
            default:
                return 'unknown';
        }
    }
} 