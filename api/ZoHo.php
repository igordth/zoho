<?php
namespace api;

class ZoHo extends BaseZoHo
{
    const XML_TYPE = 'xml';
    const JSON_TYPE = 'json';

    const FIELDS_TYPE_ALL = 1;
    const FIELDS_TYPE_MANDATORY = 2;

    public function __construct($return_type = 'xml', $auth_token = null)
    {
        if ($auth_token) {
            $this->auth_token = $auth_token;
        }
        if (!in_array($return_type, [self::XML_TYPE, self::JSON_TYPE])) {
            throw new \Exception('Current return type not allowed set it to xml or json');
        }
        $this->type = $return_type;
    }

    /**
     * Get all records from current section
     * @link https://www.zoho.com/crm/help/api/getrecords.html
     * @param string $section
     * @return string xml|json
     */
    public function getRecords($section)
    {
        $url=$this->getUrl($section, 'getRecords');
        return $this->sendGet($url);
    }

    /**
     * Get all records by record ID or IDs
     * @link https://www.zoho.com/crm/help/api/getrecordbyid.html
     * @param string $section
     * @param string|array $id
     * @return string xml|json
     */
    public function getRecordById($section, $id)
    {
        $url=$this->getUrl($section, 'getRecordById');
        $fields = [];
        if (is_array($id)) {
            $fields['idlist'] = implode(';', $id);
        }
        else {
            $fields['id'] = $id;
        }
        return $this->sendGet($url, $fields);
    }

    /**
     * Get all records from current section by the owner of the Authentication token specified in the API request
     * @link https://www.zoho.com/crm/help/api/getmyrecords.html
     * @param string $section
     * @return string xml|json
     */
    public function getMyRecords($section)
    {
        $url=$this->getUrl($section, 'getMyRecords');
        return $this->sendGet($url);
    }

    /**
     * Search the records that match your search criteria
     * @link https://www.zoho.com/crm/help/api/searchrecords.html
     * @param string $section
     * @param string|array $criteria the search criteria
     * @return string xml|json
     */
    public function searchRecords($section, $criteria)
    {
        $url=$this->getUrl($section, 'searchRecords');
        if (is_array($criteria)) {
            $map = array_map(function($k, $v){
                return $k . ':' . $v;
            }, array_keys($criteria), $criteria);
            $criteria = '((' . implode(')AND(', $map) . '))';
        }
        $fields = [
            'criteria' => $criteria,
            'selectColumns' => 'All',
        ];
        return $this->sendGet($url, $fields);
    }

    /**
     * Search the values based on pre-defined columns and custom lookup fields
     * @link https://www.zoho.com/crm/help/api/getsearchrecordsbypdc.html
     * @param string $section
     * @param string $field_name search in field
     * @param string $field_val search value
     * @param string|array $field_select show fields in result
     * @return string xml|json
     */
    public function getSearchRecordsByPDC($section, $field_name, $field_val, $field_select = 'All')
    {
        $url=$this->getUrl($section, 'getSearchRecordsByPDC');
        if (is_array($field_select)) {
            $field_select = 'Contacts(' . implode(',', $field_select) . ')';
        }
        $fields = [
            'selectColumns' => $field_select,
            'searchColumn' => $field_name,
            'searchValue' => $field_val,
        ];
        return $this->sendGet($url, $fields);
    }

    /**
     * Insert records into the required Zoho CRM module
     * @link https://www.zoho.com/crm/help/api/insertrecords.html
     * @param string $section
     * @param array $data Specify fields and corresponding values
     * @return string xml
     */
    public function insertRecords($section, array $data)
    {
        $url=$this->getUrl($section, 'insertRecords');
        $xml = $this->makeXml($section, $data);
        $fields = [
            'xmlData' => $xml,
        ];
        return $this->sendPost($url, $fields);
    }

    /**
     * Update or modify the records in Zoho CRM
     * @link https://www.zoho.com/crm/help/api/updaterecords.html
     * @param string $section
     * @param string $id Specify unique ID of the record
     * @param array $data Specify fields and corresponding values
     * @return string xml
     */
    public function updateRecords($section, $id, array $data)
    {
        $url=$this->getUrl($section, 'updateRecords');
        $xml = $this->makeXml($section, $data);
        $fields = [
            'id' => $id,
            'xmlData' => $xml,
        ];
        return $this->sendPost($url, $fields);
    }

    /**
     * Delete the selected record
     * @link https://www.zoho.com/crm/help/api/deleterecords.html
     * @param string $section
     * @param string $id Specify unique ID of the record
     * @return string xml
     */
    public function deleteRecords($section, $id)
    {
        $url=$this->getUrl($section, 'deleteRecords');
        $fields = [
            'id' => $id,
        ];
        return $this->sendGet($url, $fields);
    }

    /**
     * Return fields available in a particular module
     * @link https://www.zoho.com/crm/help/api/getfields.html
     * @param string $section
     * @param integer $type Specify the value as 1 or 2
     *      null - all fields
     *      1 - To retrieve all fields from the summary view
     *      2 - To retrieve all mandatory fields from the module
     * @return string xml|json
     */
    public function getFields($section, $type = null)
    {
        $url=$this->getUrl($section, 'getFields');
        $fields = [];
        if ($type) {
            $fields = [
                'type' => $type,
            ];
        }
        return $this->sendGet($url, $fields);
    }
}
