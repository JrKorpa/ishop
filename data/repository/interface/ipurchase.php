<?php

interface ipurchase {
    public function get_qiban_info($qiban_sn, $where);
    public function set_qiban_info($where);
    public function get_qiban_byaddtime($qiban_sn, $where);
}

