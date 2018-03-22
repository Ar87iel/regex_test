<?php
/**
 * @category WebPT
 * @copyright Copyright (c) 2013 WebPT, INC
 * @author kevinkucera
 * 4/29/13 4:49 PM
 */
namespace Console\Etl\SqlConnector\Legacy;

use EMRCore\SqlConnector\SqlConnectorAbstract;

interface ReaderWriterDiInterface {
    public function setLegacyReaderWriter(SqlConnectorAbstract $database);
}