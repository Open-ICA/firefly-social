<?php
interface model_idatabase{
	function __construct(string $servername,string $username,string $password,string $database);
	function query(string $sql);
}
class model_mysql_database implements model_idatabase {
	private $dbconnect;
	function __construct(string $servername,string $username,string $password,string $database){
		$conn = new mysqli($servername, $username, $password, $database);
		$this->dbconnect = $conn;
	}
	function __destruct(){
		$this->dbconnect = null;
	}
	function query(string $sql){
		$result = $this->dbconnect->query($sql);
		if(is_bool($result)){
			return array("status"=>$result?1:0,"data"=>[]);
		}
		$reh = array();
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_assoc($result)) {
				$reh[] = $row;
			}
		} else {
			return array("status"=>0,"data"=>[]);
		}
		return array("status"=>1,"data"=>$reh);
	}
}
?>