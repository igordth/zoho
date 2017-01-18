<?php
/**
 * Interface for fields
 */

namespace api\fields;


interface Field
{
    public function render(\XMLWriter $xml_writer);
}