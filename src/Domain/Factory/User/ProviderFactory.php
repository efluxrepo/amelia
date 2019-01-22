<?php

namespace AmeliaBooking\Domain\Factory\User;

use AmeliaBooking\Domain\Collection\Collection;

/**
 * Class ProviderFactory
 *
 * @package AmeliaBooking\Domain\Factory\User
 */
class ProviderFactory extends UserFactory
{
    /**
     * @param array $rows
     *
     * @return Collection
     * @throws \AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException
     */
    public static function createCollection($rows)
    {
        $users = [];

        foreach ($rows as $row) {
            $userId = $row['user_id'];
            $weekDayId = isset($row['weekDay_id']) ? $row['weekDay_id'] : null;
            $timeOutId = isset($row['timeOut_id']) ? $row['timeOut_id'] : null;
            $dayOffId = isset($row['dayOff_id']) ? $row['dayOff_id'] : null;
            $serviceId = isset($row['service_id']) ? $row['service_id'] : null;
            $extraId = isset($row['extra_id']) ? $row['extra_id'] : null;
            $couponId = isset($row['coupon_id']) ? $row['coupon_id'] : null;
            $googleCalendarId = isset($row['google_calendar_id']) ? $row['google_calendar_id'] : null;
            $users[$userId]['type'] = 'provider';

            $users[$userId]['id'] = $row['user_id'];
            $users[$userId]['status'] = isset($row['user_status']) ? $row['user_status'] : null;
            $users[$userId]['externalId'] = isset($row['external_id']) ? $row['external_id'] : null;
            $users[$userId]['firstName'] = $row['user_firstName'];
            $users[$userId]['lastName'] = $row['user_lastName'];
            $users[$userId]['email'] = $row['user_email'];
            $users[$userId]['note'] = isset($row['note']) ? $row['note'] : null;
            $users[$userId]['phone'] = isset($row['phone']) ? $row['phone'] : null;
            $users[$userId]['locationId'] = isset($row['user_locationId']) ? $row['user_locationId'] : null;
            $users[$userId]['pictureFullPath'] = isset($row['picture_full_path']) ? $row['picture_full_path'] : null;
            $users[$userId]['pictureThumbPath'] = isset($row['picture_thumb_path']) ? $row['picture_thumb_path'] : null;
            if ($weekDayId) {
                $users[$userId]['weekDayList'][$weekDayId]['id'] = $row['weekDay_id'];
                $users[$userId]['weekDayList'][$weekDayId]['dayIndex'] = $row['weekDay_dayIndex'];
                $users[$userId]['weekDayList'][$weekDayId]['startTime'] = $row['weekDay_startTime'];
                $users[$userId]['weekDayList'][$weekDayId]['endTime'] = $row['weekDay_endTime'];
            }

            if ($timeOutId) {
                $users[$userId]['weekDayList'][$weekDayId]['timeOutList'][$timeOutId]['id'] = $row['timeOut_id'];
                $users[$userId]['weekDayList'][$weekDayId]['timeOutList'][$timeOutId]['startTime']
                    = $row['timeOut_startTime'];
                $users[$userId]['weekDayList'][$weekDayId]['timeOutList'][$timeOutId]['endTime']
                    = $row['timeOut_endTime'];
            }

            if ($dayOffId) {
                $users[$userId]['dayOffList'][$dayOffId]['id'] = $row['dayOff_id'];
                $users[$userId]['dayOffList'][$dayOffId]['name'] = $row['dayOff_name'];
                $users[$userId]['dayOffList'][$dayOffId]['startDate'] = $row['dayOff_startDate'];
                $users[$userId]['dayOffList'][$dayOffId]['endDate'] = $row['dayOff_endDate'];
                $users[$userId]['dayOffList'][$dayOffId]['repeat'] = $row['dayOff_repeat'];
            }

            if ($serviceId) {
                $users[$userId]['serviceList'][$serviceId]['id'] = $row['service_id'];
                $users[$userId]['serviceList'][$serviceId]['name'] = $row['service_name'];
                $users[$userId]['serviceList'][$serviceId]['description'] = $row['service_description'];
                $users[$userId]['serviceList'][$serviceId]['color'] = $row['service_color'];
                $users[$userId]['serviceList'][$serviceId]['status'] = $row['service_status'];
                $users[$userId]['serviceList'][$serviceId]['categoryId'] = $row['service_categoryId'];
                $users[$userId]['serviceList'][$serviceId]['duration'] = $row['service_duration'];
                $users[$userId]['serviceList'][$serviceId]['price'] = $row['service_price'];
                $users[$userId]['serviceList'][$serviceId]['minCapacity'] = $row['service_minCapacity'];
                $users[$userId]['serviceList'][$serviceId]['maxCapacity'] = $row['service_maxCapacity'];
                $users[$userId]['serviceList'][$serviceId]['bringingAnyone'] = $row['service_bringingAnyone'];
                $users[$userId]['serviceList'][$serviceId]['pictureFullPath'] = isset($row['service_picture_full']) ?
                    $row['service_picture_full'] : null;
                $users[$userId]['serviceList'][$serviceId]['pictureThumbPath'] = isset($row['service_picture_thumb']) ?
                    $row['service_picture_thumb'] : null;

                if ($extraId) {
                    $users[$userId]['serviceList'][$serviceId]['extras'][$extraId] = [
                        'id'          => $row['extra_id'],
                        'name'        => $row['extra_name'],
                        'price'       => $row['extra_price'],
                        'maxQuantity' => $row['extra_maxQuantity'],
                        'position'    => $row['extra_position'],
                        'description' => $row['extra_description']
                    ];
                }

                if ($couponId) {
                    $users[$userId]['serviceList'][$serviceId]['coupons'][$couponId] = [
                        'id'        => $row['coupon_id'],
                        'code'      => $row['coupon_code'],
                        'discount'  => $row['coupon_discount'],
                        'deduction' => $row['coupon_deduction'],
                        'limit'     => $row['coupon_limit'],
                        'status'    => $row['coupon_status']
                    ];
                }
            }

            if ($googleCalendarId) {
                $users[$userId]['googleCalendar']['id'] = $row['google_calendar_id'];
                $users[$userId]['googleCalendar']['token'] = $row['google_calendar_token'];
                $users[$userId]['googleCalendar']['calendarId'] = isset($row['google_calendar_calendar_id']) ?
                    $row['google_calendar_calendar_id'] : null;
            }
        }

        $providersCollection = new Collection();

        foreach ($users as $providerKey => $providerArray) {
            $providersCollection->addItem(
                self::create($providerArray),
                $providerKey
            );
        }

        return $providersCollection;
    }
}
