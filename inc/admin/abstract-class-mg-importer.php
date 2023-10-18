<?php

abstract class MG_Importer {
    abstract public static function render_page() : void;
    abstract public static function get_importer_type() : string;
    abstract public function import( array $data ) : void;
    abstract public function delete_all() : void;
}
