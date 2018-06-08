<?php
require_once('includes/config.php');

try {
    $device = json_decode($_POST['info'], true);
    setGuestDeviceInfo($device);
} catch (Exception $e) {
}


function setGuestDeviceInfo($device)
{
    $query ="INSERT INTO info_devices
        (deviceModel, deviceName, deviceType, deviceUniqueIdentifier, graphicsDeviceID, graphicsDeviceName, graphicsDeviceVendor, graphicsDeviceVendorID, graphicsDeviceVersion, graphicsMemorySize, operatingSystem, operatingSystemFamily, processorCount, processorFrequency, processorType, supportedRenderTargetCount, data_reg)
    VALUES
        (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, NOW() )
    ON DUPLICATE KEY UPDATE
        deviceUniqueIdentifier = ?;";
    if ($stmt = $GLOBALS['connessione']->prepare($query)) {
        $stmt->bind_param('sssssssssssssssss', $device['_deviceModel'], $device['_deviceName'], $device['_deviceType'], $device['_deviceUniqueIdentifier'], $device['_graphicsDeviceID'], $device['_graphicsDeviceName'], $device['_graphicsDeviceVendor'], $device['_graphicsDeviceVendorID'], $device['_graphicsDeviceVersion'], $device['_graphicsMemorySize'], $device['_operatingSystem'], $device['_operatingSystemFamily'], $device['_processorCount'], $device['_processorFrequency'], $device['_processorType'], $device['_supportedRenderTargetCount'], $device['_deviceUniqueIdentifier']);
        $stmt->execute();
    }
}
