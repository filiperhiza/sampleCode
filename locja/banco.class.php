<?php

class Banco {
    private $_host;
    private $_usuario;
    private $_senha;
    private $_db;
    private $_stmt = null;
    private $_dbh = null;
    
    public function __construct( $host, $usuario, $senha, $db, $gerarErro = true ) {
        
        $this->_host = $host;
        $this->_usuario = $usuario;
        $this->_senha = $senha;
        $this->_db = $db;
        $this->_stmt = array();
        
        //Montado a string de conexão
        $dsn = 'mysql:dbname=' . $db . ';host=' . $host;
        
        try {
            $this->_dbh = new PDO( $dsn, $usuario, $senha );
            $this->_dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            $this->_dbh->setAttribute( PDO::ATTR_EMULATE_PREPARES, true );
            $this->_dbh->setAttribute( PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true );
            
        } catch (PDOException $e) {
            
            if ( $gerarErro ) {
                trigger_error( $e->getMessage() );
            } else {
                return false;
            }
        }
    }
    
    public function execute( $qry ) {
        try {
            $this->_dbh->query( $qry );
        } catch ( PDOException $e ) {
            trigger_error( $e->getMessage(), E_USER_NOTICE );
        }
    }
    
    public function query( $qry, $data = "load" ) {
        try {
            $this->stmt[$data] = $this->_dbh->query( $qry );
        } catch ( PDOException $e ) {
            trigger_error( $e->getMessage(), E_USER_NOTICE );
        }
    }
    
    public function getFetchAssoc( $data = "load" ) {
        
        if ( !isset( $this->stmt[$data] ) ) {
            trigger_error( "Nome de resultset não econtrado. Verifique o nome informado no parâmetro" );
            die;
        }
        
        return $this->stmt[$data]->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getFetchObj( $data = "load" ) {
        return $this->stmt[$data]->fetch(PDO::FETCH_OBJ);
    }
    
    public function getNumRows( $data = "load" ) {
        return $this->stmt[$data]->rowCount();
    }
    
    public function getLastInsertId( ) {
        return $this->_dbh->lastInsertId();
    }
    
    public function getFetchColumn( $data = "load" ) {
        return $this->stmt[$data]->fetchColumn();
    }
    
    public function isConnected() {
        return (bool)$this->_dbh;
    }
}
