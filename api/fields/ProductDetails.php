<?php
/**
 * ProductDetails
 */

namespace api\fields;


class ProductDetails implements Field
{
    private $attributes = [
        'Product Id',
        'Product Name',
        'Unit Price',
        'Quantity',
        'Quantity in Stock',
        'Total',
        'Discount',
        'Total After Discount',
        'List Price',
        'Net Total',
        'Tax',
        'Product Description',
    ];
    private $products;

    public function addProduct(array $attributes)
    {
        $diff = array_diff(array_keys($attributes), $this->attributes);
        if (!empty($diff)) {
            throw new \Exception('This fields not exist: <pre>' . print_r($diff, 1) . '</pre>');
        }
        $this->products[] = $attributes;
    }

    public function getProducts()
    {
        return $this->products;
    }
    
    public function render(\XMLWriter $xml_writer)
    {
        $no = 0;
        foreach ($this->getProducts() as $product) {
            $xml_writer->startElement('product');
            $xml_writer->writeAttribute('no', ++$no);
            foreach ($product as $key => $val) {
                $xml_writer->startElement('FL');
                $xml_writer->writeAttribute('val', $key);
                $xml_writer->text($val);
                $xml_writer->endElement();
            }
            $xml_writer->endElement();
        }
    }

}