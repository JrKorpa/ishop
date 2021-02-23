<?php

interface imanagement {
    public function get_dictlist($where);
    public function get_sources_list($ids);
    public function get_customer_sources_list($where);
}

