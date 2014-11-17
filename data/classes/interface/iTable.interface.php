<?php

interface iTable {
    static function getColumns();
    static function getDefaultColumns();
    public function getColumn( $column );
    public function getColumnName( $column );
    public function getOrderByColumn( $column );
}