<?php

namespace Scue\Geo\Plugin;

use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\App\Config\ScopeConfigInterface;

class GeodataLogPlugin
{

    private RemoteAddress $address;

    private DirectoryList $dir;

    private ScopeConfigInterface $scopeConfig;

    const LOG_FILE = 'geodata_requests.log';

    public function __construct(
        RemoteAddress $address,
        DirectoryList $dir,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->address = $address;
        $this->dir = $dir;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $subject
     * @param $url
     * @param $bearerToken
     * @return array
     */
    public function beforeSendGet($subject, $url, $bearerToken = '')
    {
        if ($this->getGeodataRequestsLogValue()) {

            $logFileName = $this->dir->getPath('log') . '/' . self::LOG_FILE;

            $ipAddress = $this->getIpAddress();

            $logData = [
                'url' => $url,
                'datetime' => date('Y-m-d H:i:s'),
                'IPaddress' => $ipAddress,
                'request_function' => 'sendGet',
            ];

            $logMessage = "DateTime: {$logData['datetime']}, URL: {$logData['url']}, Request function: {$logData['request_function']}, User IP: {$logData['IPaddress']}\n";

            file_put_contents($logFileName, $logMessage, FILE_APPEND);
        }

        return [$url, $bearerToken];
    }

    public function beforeSendPost($subject, $url, $params, $bearerToken = '')
    {
        if ($this->getGeodataRequestsLogValue()) {
            $logFileName = $this->dir->getPath('log') . '/' . self::LOG_FILE;

            $ipAddress = $this->getIpAddress();

            $logData = [
                'url' => $url,
                'datetime' => date('Y-m-d H:i:s'),
                'IPaddress' => $ipAddress,
                'request_function' => 'sendPost',
            ];

            $logMessage = "DateTime: {$logData['datetime']}, URL: {$logData['url']}, Request function: {$logData['request_function']}, User IP: {$logData['IPaddress']}\n";

            file_put_contents($logFileName, $logMessage, FILE_APPEND);
        }

        return [$url, $params, $bearerToken];
    }

    /**
     * Get the client IP address.
     *
     * @return string
     */
    public function getIpAddress()
    {
        $ip = $this->address->getRemoteAddress();
        return $ip ?? 'IP Address not defined';
    }

    public function getGeodataRequestsLogValue()
    {
        $path = 'global_geodata_params/geodata/geodata_requests';

        return $this->scopeConfig->getValue($path);
    }

}
