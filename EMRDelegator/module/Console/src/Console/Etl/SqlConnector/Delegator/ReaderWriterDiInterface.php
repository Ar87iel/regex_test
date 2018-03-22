<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author kevinKucera
 * 4/29/13 4:49 PM
 */
namespace Console\Etl\SqlConnector\Delegator;
use EMRCore\SqlConnector\SqlConnectorAbstract;

interface ReaderWriterDiInterface {
    public function setDelegatorReaderWriter(SqlConnectorAbstract $database);
}