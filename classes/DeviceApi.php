<?php
interface DeviceApi {
    public function getAlarms();
    public function getData();
    public function getInfo();
    public function syncTime();
}
?>