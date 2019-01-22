<?php

namespace AmeliaBooking\Infrastructure\Repository\User;

use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\User\AbstractUser;
use AmeliaBooking\Domain\Entity\User\Provider;
use AmeliaBooking\Domain\Factory\User\ProviderFactory;
use AmeliaBooking\Domain\Services\DateTime\DateTimeService;
use AmeliaBooking\Domain\ValueObjects\String\Status;
use AmeliaBooking\Domain\Repository\User\ProviderRepositoryInterface;
use AmeliaBooking\Infrastructure\Common\Exceptions\NotFoundException;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Connection;
use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Bookable\ExtrasTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Booking\AppointmentsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Coupon\CouponsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Coupon\CouponsToServicesTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Location\LocationsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\User\WPUsersTable;

/**
 * Class ProviderRepository
 *
 * @package AmeliaBooking\Infrastructure\Repository
 */
class ProviderRepository extends UserRepository implements ProviderRepositoryInterface
{
    const FACTORY = ProviderFactory::class;

    /** @var string */
    protected $providerWeekDayTable;

    /** @var string */
    protected $providerTimeOutTable;

    /** @var string */
    protected $providerDayOffTable;

    /** @var string */
    protected $providerServicesTable;

    /** @var string */
    protected $providerLocationTable;

    /** @var string */
    protected $serviceTable;

    /** @var string */
    protected $providerViewsTable;

    /** @var string */
    protected $providersGoogleCalendarTable;

    /**
     * @param Connection $connection
     * @param string     $table
     * @param string     $providerWeekDayTable
     * @param string     $providerTimeOutTable
     * @param string     $providerDayOffTable
     * @param string     $providerServicesTable
     * @param string     $providerLocationTable
     * @param string     $serviceTable
     * @param string     $providerViewsTable
     * @param string     $providersGoogleCalendarTable
     */
    public function __construct(
        Connection $connection,
        $table,
        $providerWeekDayTable,
        $providerTimeOutTable,
        $providerDayOffTable,
        $providerServicesTable,
        $providerLocationTable,
        $serviceTable,
        $providerViewsTable,
        $providersGoogleCalendarTable
    ) {
        parent::__construct($connection, $table);

        $this->providerWeekDayTable = $providerWeekDayTable;
        $this->providerTimeOutTable = $providerTimeOutTable;
        $this->providerDayOffTable = $providerDayOffTable;
        $this->providerServicesTable = $providerServicesTable;
        $this->providerLocationTable = $providerLocationTable;
        $this->serviceTable = $serviceTable;
        $this->providerViewsTable = $providerViewsTable;
        $this->providersGoogleCalendarTable = $providersGoogleCalendarTable;
    }

    /**
     * @param int $id
     *
     * @return Provider
     * @throws QueryExecutionException
     * @throws NotFoundException
     */
    public function getById($id)
    {
        try {
            $statement = $this->connection->prepare(
                "SELECT
                    u.id AS user_id,
                    u.status AS user_status,
                    u.externalId AS external_id,
                    u.firstName AS user_firstName,
                    u.lastName AS user_lastName,
                    u.email AS user_email,
                    u.note AS note,
                    u.phone AS phone,
                    u.pictureFullPath AS picture_full_path,
                    u.pictureThumbPath AS picture_thumb_path,
                    lt.locationId AS user_locationId,
                    wdt.id AS weekDay_id,
                    wdt.dayIndex AS weekDay_dayIndex,
                    wdt.startTime AS weekDay_startTime,
                    wdt.endTime As weekDay_endTime,
                    tot.id AS timeOut_id,
                    tot.startTime AS timeOut_startTime,
                    tot.endTime AS timeOut_endTime,
                    dot.id AS dayOff_id,
                    dot.name AS dayOff_name,
                    dot.startDate AS dayOff_startDate,
                    dot.endDate AS dayOff_endDate,
                    dot.repeat AS dayOff_repeat,
                    st.serviceId AS service_id,
                    st.price AS service_price,
                    st.minCapacity AS service_minCapacity,
                    st.maxCapacity AS service_maxCapacity,
                    s.name AS service_name,
                    s.description AS service_description,
                    s.color AS service_color,
                    s.status AS service_status,
                    s.categoryId AS service_categoryId,
                    s.duration AS service_duration,
                    s.bringingAnyone AS service_bringingAnyone,
                    gd.id AS google_calendar_id,
                    gd.token AS google_calendar_token,
                    gd.calendarId AS google_calendar_calendar_id
                FROM {$this->table} u
                LEFT JOIN {$this->providerServicesTable} st ON st.userId = u.id
                LEFT JOIN {$this->serviceTable} s ON s.id = st.serviceId
                LEFT JOIN {$this->providerLocationTable} lt ON lt.userId = u.id
                LEFT JOIN {$this->providersGoogleCalendarTable} gd ON gd.userId = u.id
                LEFT JOIN {$this->providerWeekDayTable} wdt ON wdt.userId = u.id
                LEFT JOIN {$this->providerDayOffTable} dot ON dot.userId = u.id
                LEFT JOIN {$this->providerTimeOutTable} tot ON tot.weekDayId = wdt.id
                WHERE u.type = :type AND u.id = :userId
                ORDER BY u.id, tot.weekDayId, wdt.dayIndex"
            );

            $type = AbstractUser::USER_ROLE_PROVIDER;

            $statement->bindParam(':type', $type);
            $statement->bindParam(':userId', $id);

            $statement->execute();

            $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__, $e->getCode(), $e);
        }

        if (!$rows) {
            throw new NotFoundException('Data not found in ' . __CLASS__);
        }

        return call_user_func([static::FACTORY, 'createCollection'], $rows, 'provider')->getItem($id);
    }

    /**
     *
     * @return Collection
     * @throws QueryExecutionException
     */
    public function getAll()
    {
        try {
            $statement = $this->connection->prepare(
                "SELECT
                    u.id AS user_id,
                    u.status AS user_status,
                    u.externalId AS external_id,
                    u.firstName AS user_firstName,
                    u.lastName AS user_lastName,
                    u.email AS user_email,
                    u.note AS note,
                    u.phone AS phone,
                    u.pictureFullPath AS picture_full_path,
                    u.pictureThumbPath AS picture_thumb_path,
                    lt.locationId AS user_locationId,
                    wdt.id AS weekDay_id,
                    wdt.dayIndex AS weekDay_dayIndex,
                    wdt.startTime AS weekDay_startTime,
                    wdt.endTime As weekDay_endTime,
                    tot.id AS timeOut_id,
                    tot.startTime AS timeOut_startTime,
                    tot.endTime AS timeOut_endTime,
                    dot.id AS dayOff_id,
                    dot.name AS dayOff_name,
                    dot.startDate AS dayOff_startDate,
                    dot.endDate AS dayOff_endDate,
                    dot.repeat AS dayOff_repeat,
                    st.serviceId AS service_id,
                    st.price AS service_price,
                    st.minCapacity AS service_minCapacity,
                    st.maxCapacity AS service_maxCapacity,
                    s.name AS service_name,
                    s.description AS service_description,
                    s.color AS service_color,
                    s.status AS service_status,
                    s.categoryId AS service_categoryId,
                    s.duration AS service_duration,
                    s.bringingAnyone AS service_bringingAnyone,
                    s.pictureFullPath AS service_picture_full,
                    s.pictureThumbPath AS service_picture_thumb
                FROM {$this->table} u
                LEFT JOIN {$this->providerServicesTable} st ON st.userId = u.id
                LEFT JOIN {$this->serviceTable} s ON s.id = st.serviceId
                LEFT JOIN {$this->providerLocationTable} lt ON lt.userId = u.id
                LEFT JOIN {$this->providerWeekDayTable} wdt ON wdt.userId = u.id
                LEFT JOIN {$this->providerDayOffTable} dot ON dot.userId = u.id
                LEFT JOIN {$this->providerTimeOutTable} tot ON tot.weekDayId = wdt.id
                WHERE u.type = :type
                ORDER BY CONCAT(u.firstName, ' ', u.lastName), tot.weekDayId, wdt.dayIndex"
            );

            $type = AbstractUser::USER_ROLE_PROVIDER;

            $statement->bindParam(':type', $type);

            $statement->execute();

            $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__, $e->getCode(), $e);
        }

        return call_user_func([static::FACTORY, 'createCollection'], $rows);
    }

    /**
     * @param array $criteria
     * @param int   $itemsPerPage
     *
     * @return Collection
     * @throws QueryExecutionException
     * @throws InvalidArgumentException
     */
    public function getFiltered($criteria, $itemsPerPage)
    {
        try {
            $wpUserTable = WPUsersTable::getTableName();

            $params[':type'] = AbstractUser::USER_ROLE_PROVIDER;

            $limit = '';

            if (!empty($criteria['page'])) {
                $params[':startingLimit'] = ($criteria['page'] - 1) * $itemsPerPage;
                $limit = 'LIMIT :startingLimit';
            }

            if ($itemsPerPage && $criteria['page']) {
                $params[':itemsPerPage'] = $itemsPerPage;
                $limit .= ', :itemsPerPage';
            }

            $order = '';
            if (!empty($criteria['sort'])) {
                $orderColumn = 'CONCAT(firstName, " ", lastName)';
                $orderDirection = $criteria['sort'][0] === '-' ? 'DESC' : 'ASC';
                $order = "ORDER BY {$orderColumn} {$orderDirection}";
            }

            $where = [];

            if (!empty($criteria['search'])) {
                $params[':search1'] = $params[':search2'] = $params[':search3'] = "%{$criteria['search']}%";

                $where[] = "u.id IN(
                    SELECT DISTINCT(user.id)
                        FROM {$this->table} user
                        LEFT JOIN {$wpUserTable} wpUser ON user.externalId = wpUser.ID
                        WHERE (CONCAT(user.firstName, ' ', user.lastName) LIKE :search1
                            OR wpUser.display_name LIKE :search2
                            OR user.note LIKE :search3)
                    )";
            }

            if (!empty($criteria['services'])) {
                $queryServices = [];

                foreach ((array)$criteria['services'] as $index => $value) {
                    $param = ':service' . $index;
                    $queryServices[] = $param;
                    $params[$param] = $value;
                }

                $where[] = "id IN (
                    SELECT userId FROM {$this->providerServicesTable}
                    WHERE userId = u.id AND serviceId IN (" . implode(', ', $queryServices) . ')
                )';
            }

            if (!empty($criteria['providers'])) {
                $queryProviders = [];

                foreach ((array)$criteria['providers'] as $index => $value) {
                    $param = ':provider' . $index;
                    $queryProviders[] = $param;
                    $params[$param] = $value;
                }

                $where[] = 'id IN (' . implode(', ', $queryProviders) . ')';
            }

            if (!empty($criteria['location'])) {
                $params[':location'] = $criteria['location'];

                $where[] = "id IN (
                    SELECT userId FROM {$this->providerLocationTable}
                    WHERE userId = u.id AND locationId = :location)";
            }

            $where[] = "u.status NOT LIKE 'disabled'";

            $where = $where ? ' AND ' . implode(' AND ', $where) : '';

            $statement = $this->connection->prepare(
                "SELECT u.*
                    FROM {$this->table} u
                    WHERE u.type = :type $where
                {$order}
                {$limit}"
            );

            $statement->execute($params);

            $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to get data from ' . __CLASS__, $e->getCode(), $e);
        }

        $items = [];
        foreach ($rows as $row) {
            $items[] = call_user_func([static::FACTORY, 'create'], $row);
        }

        return new Collection($items);
    }

    /**
     * @param array $criteria
     *
     * @return mixed
     * @throws QueryExecutionException
     */
    public function getCount($criteria)
    {
        $params = [
            ':type'          => AbstractUser::USER_ROLE_PROVIDER,
            ':visibleStatus' => Status::VISIBLE,
            ':hiddenStatus'  => Status::HIDDEN,
        ];

        try {
            $wpUserTable = WPUsersTable::getTableName();

            $where = [];

            if (!empty($criteria['search'])) {
                $params[':search1'] = $params[':search2'] = $params[':search3'] = "%{$criteria['search']}%";

                $where[] = "u.id IN(
                    SELECT DISTINCT(user.id)
                        FROM {$this->table} user
                        LEFT JOIN {$wpUserTable} wpUser ON user.externalId = wpUser.ID
                        WHERE (CONCAT(user.firstName, ' ', user.lastName) LIKE :search1
                            OR wpUser.display_name LIKE :search2
                            OR user.note LIKE :search3)
                    )";
            }

            if (!empty($criteria['services'])) {
                $queryServices = [];

                foreach ((array)$criteria['services'] as $index => $value) {
                    $param = ':service' . $index;
                    $queryServices[] = $param;
                    $params[$param] = $value;
                }

                $where[] = "id IN (
                    SELECT userId FROM {$this->providerServicesTable}
                    WHERE userId = u.id AND serviceId IN (" . implode(', ', $queryServices) . ')
                )';
            }

            if (!empty($criteria['location'])) {
                $params[':location'] = $criteria['location'];

                $where[] = "id IN (
                    SELECT userId FROM {$this->providerLocationTable}
                    WHERE userId = u.id AND locationId = :location)";
            }

            $where = $where ? ' AND ' . implode(' AND ', $where) : '';

            $statement = $this->connection->prepare(
                "SELECT COUNT(*) AS count
                    FROM {$this->table} u
                    WHERE u.type = :type AND u.status IN (:visibleStatus, :hiddenStatus) $where"
            );

            $statement->execute($params);

            $row = $statement->fetch(\PDO::FETCH_ASSOC)['count'];
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to get data from ' . __CLASS__, $e->getCode(), $e);
        }

        return $row;
    }

    /**
     * @param      $weekDayIndexes
     * @param      $criteria
     *
     * @return Collection
     *
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     */
    public function getByWeekDays($weekDayIndexes, $criteria)
    {
        $locationsTable = LocationsTable::getTableName();

        $params = [
            ':type' => AbstractUser::USER_ROLE_PROVIDER,
        ];

        $where = [];

        $days = implode(',', $weekDayIndexes);

        if (!empty($criteria['services'])) {
            $queryServices = [];

            foreach ((array)$criteria['services'] as $index => $value) {
                $param = ':service' . $index;
                $queryServices[] = $param;
                $params[$param] = $value;
            }

            $where[] = 'st.serviceId IN (' . implode(', ', $queryServices) . ')';
        }

        if (!empty($criteria['providers'])) {
            $queryProviders = [];

            foreach ((array)$criteria['providers'] as $index => $value) {
                $param = ':provider' . $index;
                $queryProviders[] = $param;
                $params[$param] = $value;
            }

            $where[] = 'u.id IN (' . implode(', ', $queryProviders) . ')';
        }

        if (!empty($criteria['location'])) {
            $params[':locationId'] = $criteria['location'];

            $where[] = 'lt.locationId = :locationId';
        }

        $where = $where ? ' AND ' . implode(' AND ', $where) : '';

        try {
            $statement = $this->connection->prepare(
                "SELECT
                    u.id AS user_id,
                    u.firstName AS user_firstName,
                    u.lastName AS user_lastName,
                    u.email AS user_email,
                    lt.locationId AS user_locationId,
                    wdt.id AS weekDay_id,
                    wdt.dayIndex AS weekDay_dayIndex,
                    wdt.startTime AS weekDay_startTime,
                    wdt.endTime As weekDay_endTime,
                    tot.id AS timeOut_id,
                    tot.startTime AS timeOut_startTime,
                    tot.endTime AS timeOut_endTime,
                    dot.id AS dayOff_id,
                    dot.name AS dayOff_name,
                    dot.startDate AS dayOff_startDate,
                    dot.endDate AS dayOff_endDate,
                    dot.repeat AS dayOff_repeat,
                    st.serviceId AS service_id,
                    st.price AS service_price,
                    st.minCapacity AS service_minCapacity,
                    st.maxCapacity AS service_maxCapacity,
                    s.name AS service_name,
                    s.description AS service_description,
                    s.color AS service_color,
                    s.status AS service_status,
                    s.categoryId AS service_categoryId,
                    s.duration AS service_duration,
                    s.bringingAnyone AS service_bringingAnyone,
                    s.pictureFullPath AS service_picture_full,
                    s.pictureThumbPath AS service_picture_thumb,
                    gd.id AS google_calendar_id,
                    gd.token AS google_calendar_token,
                    gd.calendarId AS google_calendar_calendar_id
                FROM {$this->table} u
                INNER JOIN {$this->providerServicesTable} st ON st.userId = u.id
                LEFT JOIN {$this->serviceTable} s ON s.id = st.serviceId
                LEFT JOIN {$this->providerLocationTable} lt ON lt.userId = u.id
                LEFT JOIN {$locationsTable} l ON (lt.locationId = l.id AND l.status = 'visible')
                LEFT JOIN {$this->providersGoogleCalendarTable} gd ON gd.userId = u.id
                LEFT JOIN {$this->providerWeekDayTable} wdt ON wdt.userId = u.id
                LEFT JOIN {$this->providerDayOffTable} dot ON dot.userId = u.id
                LEFT JOIN {$this->providerTimeOutTable} tot ON tot.weekDayId = wdt.id
                WHERE wdt.dayIndex IN ({$days}) AND u.status = 'visible'
                AND s.status = 'visible' AND u.type = :type $where
                ORDER BY tot.weekDayId, wdt.dayIndex"
            );

            $statement->execute($params);

            $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__, $e->getCode(), $e);
        }

        return call_user_func([static::FACTORY, 'createCollection'], $rows);
    }

    /**
     * @param      $criteria
     *
     * @return Collection
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     */
    public function getWithServicesAndExtras($criteria)
    {
        $extrasTable = ExtrasTable::getTableName();
        $couponToServicesTable = CouponsToServicesTable::getTableName();
        $couponsTable = CouponsTable::getTableName();

        $params = [
            ':type'          => AbstractUser::USER_ROLE_PROVIDER,
            ':userStatus'    => Status::VISIBLE,
            ':serviceStatus' => Status::VISIBLE
        ];

        $where = [];

        foreach ((array)$criteria as $index => $value) {
            $params[':service' . $index] = $value['serviceId'];
            $params[':provider' . $index] = $value['providerId'];

            if ($value['couponId']) {
                $params[':coupon' . $index] = $value['couponId'];
                $params[':couponStatus' . $index] = Status::VISIBLE;
            }

            $where[] = "(s.id = :service$index AND u.id = :provider$index"
                . ($value['couponId'] ? " AND c.id = :coupon$index AND c.status = :couponStatus$index" : '') . ')';
        }

        $where = $where ? ' AND ' . implode(' OR ', $where) : '';

        try {
            $statement = $this->connection->prepare(
                "SELECT
                    u.id AS user_id,
                    u.firstName AS user_firstName,
                    u.lastName AS user_lastName,
                    u.email AS user_email,
                    st.serviceId AS service_id,
                    st.price AS service_price,
                    st.minCapacity AS service_minCapacity,
                    st.maxCapacity AS service_maxCapacity,
                    s.name AS service_name,
                    s.description AS service_description,
                    s.color AS service_color,
                    s.status AS service_status,
                    s.categoryId AS service_categoryId,
                    s.duration AS service_duration,
                    s.bringingAnyone AS service_bringingAnyone,
                    s.pictureFullPath AS service_picture_full,
                    s.pictureThumbPath AS service_picture_thumb,
                    e.id AS extra_id,
                    e.name AS extra_name,
                    e.price AS extra_price,
                    e.maxQuantity AS extra_maxQuantity,
                    e.duration AS extra_duration,
                    e.description AS extra_description,
                    e.position AS extra_position,
                    c.id AS coupon_id,
                    c.code AS coupon_code,
                    c.discount AS coupon_discount,
                    c.deduction AS coupon_deduction,
                    c.limit AS coupon_limit,
                    c.status AS coupon_status
                FROM {$this->table} u
                INNER JOIN {$this->providerServicesTable} st ON st.userId = u.id
                INNER JOIN {$this->serviceTable} s ON s.id = st.serviceId
                LEFT JOIN {$extrasTable} e ON e.serviceId = s.id
                LEFT JOIN {$couponToServicesTable} cs ON cs.serviceId = s.id
                LEFT JOIN {$couponsTable} c ON c.id = cs.couponId
                WHERE u.status = :userStatus AND s.status = :serviceStatus AND u.type = :type $where"
            );

            $statement->execute($params);

            $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__, $e->getCode(), $e);
        }

        return call_user_func([static::FACTORY, 'createCollection'], $rows);
    }

    /**
     * Returns array of available (currently working) Providers where keys are Provider ID's and array values are
     * Working Hours Data
     *
     * @param $dayIndex
     *
     * @return array
     * @throws QueryExecutionException
     */
    public function getAvailable($dayIndex)
    {
        $currentDateTime = "STR_TO_DATE('" . DateTimeService::getNowDateTime() . "', '%Y-%m-%d %H:%i:%s')";

        $params = [
            ':dayIndex' => $dayIndex === 0 ? 7 : $dayIndex,
            ':type'     => AbstractUser::USER_ROLE_PROVIDER
        ];

        try {
            $statement = $this->connection->prepare("SELECT
                u.id AS user_id,
                u.firstName AS user_firstName,
                u.lastName AS user_lastName,
                wdt.id AS weekDay_id,
                wdt.dayIndex AS weekDay_dayIndex,
                wdt.startTime AS weekDay_startTime,
                wdt.endTime As weekDay_endTime
              FROM {$this->table} u
              LEFT JOIN {$this->providerWeekDayTable} wdt ON wdt.userId = u.id
              WHERE u.type = :type AND
              wdt.dayIndex = :dayIndex AND
              {$currentDateTime} >= wdt.startTime AND
              {$currentDateTime} <= wdt.endTime");

            $statement->execute($params);

            $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__, $e->getCode(), $e);
        }

        $result = [];

        foreach ($rows as $row) {
            $result[$row['user_id']] = $row;
        }

        return $result;
    }

    /**
     * @param $dayIndex
     *
     * @return array
     * @throws QueryExecutionException
     */
    public function getOnBreak($dayIndex)
    {
        $currentDateTime = "STR_TO_DATE('" . DateTimeService::getNowDateTime() . "', '%Y-%m-%d %H:%i:%s')";

        $params = [
            ':dayIndex' => $dayIndex === 0 ? 7 : $dayIndex,
            ':type'     => AbstractUser::USER_ROLE_PROVIDER
        ];

        try {
            $statement = $this->connection->prepare("SELECT
                u.id AS user_id,
                u.firstName AS user_firstName,
                u.lastName AS user_lastName,
                wdt.id AS weekDay_id,
                wdt.dayIndex AS weekDay_dayIndex,
                wdt.startTime AS weekDay_startTime,
                wdt.endTime As weekDay_endTime,
                tot.id AS timeOut_id,
                tot.startTime AS timeOut_startTime,
                tot.endTime AS timeOut_endTime
              FROM {$this->table} u
              LEFT JOIN {$this->providerWeekDayTable} wdt ON wdt.userId = u.id
              LEFT JOIN {$this->providerTimeOutTable} tot ON tot.weekDayId = wdt.id
              WHERE u.type = :type AND
              wdt.dayIndex = :dayIndex AND
              {$currentDateTime} >= wdt.startTime AND
              {$currentDateTime} <= wdt.endTime AND
              {$currentDateTime} >= tot.startTime AND
              {$currentDateTime} <= tot.endTime");

            $statement->execute($params);

            $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__, $e->getCode(), $e);
        }

        $result = [];

        foreach ($rows as $row) {
            $result[$row['user_id']] = $row;
        }

        return $result;
    }

    /**
     * @return array
     * @throws QueryExecutionException
     */
    public function getOnVacation()
    {
        $currentDateTime = "STR_TO_DATE('" . DateTimeService::getNowDateTime() . "', '%Y-%m-%d %H:%i:%s')";

        $params = [
            ':type' => AbstractUser::USER_ROLE_PROVIDER
        ];

        try {
            $statement = $this->connection->prepare("SELECT
                u.id,
                u.firstName,
                u.lastName,
                dot.startDate,
                dot.endDate,
                dot.name
              FROM {$this->table} u
              LEFT JOIN {$this->providerDayOffTable} dot ON dot.userId = u.id
              WHERE u.type = :type AND
              DATE_FORMAT({$currentDateTime}, '%Y-%m-%d') BETWEEN dot.startDate AND dot.endDate");

            $statement->execute($params);

            $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__, $e->getCode(), $e);
        }

        $result = [];

        foreach ($rows as $row) {
            $result[$row['id']] = $row;
        }

        return $result;
    }

    /**
     * Return an array of providers with the number of appointments for the given date period.
     * Keys of the array are Provider IDs.
     *
     * @param $criteria
     *
     * @return array
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     */
    public function getAllNumberOfAppointments($criteria)
    {
        $appointmentTable = AppointmentsTable::getTableName();

        $params = [];
        $where = [];

        if ($criteria['dates']) {
            $where[] = "(DATE_FORMAT(a.bookingStart, '%Y-%m-%d') BETWEEN :bookingFrom AND :bookingTo)";
            $params[':bookingFrom'] = DateTimeService::getCustomDateTimeInUtc($criteria['dates'][0]);
            $params[':bookingTo'] = DateTimeService::getCustomDateTimeInUtc($criteria['dates'][1]);
        }

        if (isset($criteria['status'])) {
            $where[] = 'u.status = :status';
            $params[':status'] = $criteria['status'];
        }

        $where = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        try {
            $statement = $this->connection->prepare("SELECT
                u.id,
                CONCAT(u.firstName, ' ', u.lastName) AS name,
                COUNT(a.providerId) AS appointments
            FROM {$this->table} u 
            INNER JOIN {$appointmentTable} a ON u.id = a.providerId
            $where
            GROUP BY providerId");

            $statement->execute($params);

            $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to get data from ' . __CLASS__, $e->getCode(), $e);
        }

        $result = [];

        foreach ($rows as $row) {
            $result[$row['id']] = $row;
        }

        return $result;
    }

    /**
     * Return an array of providers with the number of views for the given date period.
     * Keys of the array are Providers IDs.
     *
     * @param $criteria
     *
     * @return array
     * @throws QueryExecutionException
     */
    public function getAllNumberOfViews($criteria)
    {
        $params = [];
        $where = [];

        if ($criteria['dates']) {
            $where[] = "(DATE_FORMAT(pv.date, '%Y-%m-%d') BETWEEN :bookingFrom AND :bookingTo)";
            $params[':bookingFrom'] = DateTimeService::getCustomDateTimeInUtc($criteria['dates'][0]);
            $params[':bookingTo'] = DateTimeService::getCustomDateTimeInUtc($criteria['dates'][1]);
        }

        if (isset($criteria['status'])) {
            $where[] = 'u.status = :status';
            $params[':status'] = $criteria['status'];
        }

        $where = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        try {
            $statement = $this->connection->prepare("SELECT
            u.id,
            CONCAT(u.firstName, ' ', u.lastName) as name,
            SUM(pv.views) AS views
            FROM {$this->table} u
            INNER JOIN {$this->providerViewsTable} pv ON pv.userId = u.id 
            $where
            GROUP BY u.id");

            $statement->execute($params);

            $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to get data from ' . __CLASS__, $e->getCode(), $e);
        }

        $result = [];

        foreach ($rows as $row) {
            $result[$row['id']] = $row;
        }

        return $result;
    }

    /**
     * @param $providerId
     *
     * @return string
     * @throws QueryExecutionException
     */
    public function addViewStats($providerId)
    {
        $date = DateTimeService::getNowDate();

        $params = [
            ':userId' => $providerId,
            ':date'   => $date,
            ':views'  => 1
        ];

        try {
            // Check if there is already data for this provider for this date
            $statement = $this->connection->prepare(
                "SELECT COUNT(*) AS count 
                FROM {$this->providerViewsTable} AS pv 
                WHERE pv.userId = :userId 
                AND pv.date = :date"
            );

            $statement->bindParam(':userId', $providerId);
            $statement->bindParam(':date', $date);
            $statement->execute();
            $count = $statement->fetch(\PDO::FETCH_ASSOC)['count'];

            if (!$count) {
                $statement = $this->connection->prepare(
                    "INSERT INTO {$this->providerViewsTable}
                    (`userId`, `date`, `views`)
                    VALUES 
                    (:userId, :date, :views)"
                );
            } else {
                $statement = $this->connection->prepare(
                    "UPDATE {$this->providerViewsTable} pv SET pv.views = pv.views + :views
                    WHERE pv.userId = :userId
                    AND pv.date = :date"
                );
            }

            $response = $statement->execute($params);
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to add data in ' . __CLASS__);
        }

        if (!$response) {
            throw new QueryExecutionException('Unable to add data in ' . __CLASS__);
        }

        return true;
    }
}
