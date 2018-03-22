<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author jgiberson
 * 5/9/13 9:44 AM
 */

namespace Console\Etl\Service\Dto;


class InsertStatementResult {
    /** @var  string */
    protected $sql;

    /**
     * @param string $sql
     * @return InsertStatementResult
     */
    public function setSql($sql)
    {
        $this->sql = $sql;
        return $this;
    }

    /**
     * @return string
     */
    public function getSql()
    {
        return $this->sql;
    }

}