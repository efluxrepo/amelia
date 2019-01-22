<?php
/**
 * @author Slavko Babic
 * @date   2017-08-21
 */

namespace AmeliaBooking\Infrastructure;

use \PDO;

/**
 * Class Connection
 *
 * @package Infrastructure
 */
class Connection
{
    /** @var PDO $pdo */
    protected $pdo;

    /** @var string $username */
    protected $username;

    /** @var string $password */
    protected $password;

    /** @var string $charset */
    protected $charset;

    /** @var PDO $handler */
    protected $handler;

    /** @var int port */
    private $port;

    /** @var string $host */
    private $host;

    /** @var string $name */
    private $database;

    /** @var string $dns */
    private $dns;

    /** @var string $socket */
    private $socket;

    /** @var string $driver */
    private $driver = 'mysql';

    /**
     * Connection constructor.
     *
     * @param string $database
     * @param string $username
     * @param string $password
     * @param string $host
     * @param int    $port
     * @param string $charset
     */
    public function __construct(
        $host,
        $database,
        $username,
        $password,
        $charset = 'utf8',
        $port = 3306
    ) {
        $this->database = (string)$database;
        $this->username = (string)$username;
        $this->password = (string)$password;
        $this->host = $this->socket = (string)$host;
        $this->port = (int)$port;
        $this->charset = (string)$charset;

        $this->handler = new PDO(
            $this->dns(),
            $this->username,
            $this->password,
            $this->getOptions()
        );
        $this->handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->handler->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    /**
     * @return PDO
     */
    public function __invoke()
    {
        return $this->handler;
    }

    /**
     * @return string
     */
    private function dns()
    {
        if ($this->dns) {
            return $this->dns;
        }

        $this->socketHandler();

        return $this->dns = "{$this->driver}:host={$this->host};port={$this->port}';dbname={$this->database}";
    }

    /**
     *
     */
    private function socketHandler()
    {
        if (strpos($this->socket, ':') === false) {
            $this->host = $this->socket;

            return;
        }

        $data = explode(':', $this->socket);

        $this->host = $data[0];

        if (isset($data[1]) && (int)$data[1]) {
            $this->port = $data[1];
        }
    }

    /**
     * @return array
     */
    private function getOptions()
    {
        $options = [
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION,
        ];

        if (defined('DB_CHARSET')) {
            $options[PDO::MYSQL_ATTR_INIT_COMMAND] = 'set names ' . DB_CHARSET;
        }

        return $options;
    }
}
