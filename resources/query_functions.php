<?php
	ini_set("display_errors",1);
	require_once(dirname(__FILE__)."/mysql_connect.php");

	function perform_query($sql,$typeDef = FALSE, $params = FALSE){ 
		if(!$sql)
			return false;
		global $mysqli;
		if($stmt = mysqli_prepare($mysqli,$sql)){ 
			if(count($params) == count($params,1)){ 
				$params = array($params); 
			}else{
				return "Error Binding Params";
			}
		
			if($typeDef){ 
				$bindParams = array();    
				$bindParamsReferences = array(); 
				$bindParams = array_pad($bindParams,(count($params,1)-count($params))/count($params),"");         
				foreach($bindParams as $key => $value){ 
					$bindParamsReferences[$key] = &$bindParams[$key];  
				} 
				array_unshift($bindParamsReferences,$typeDef); 
				$bindParamsMethod = new ReflectionMethod('mysqli_stmt', 'bind_param'); 
				
				if(!$bindParamsMethod->invokeArgs($stmt,$bindParamsReferences)){
					return "Error Binding Params";
				}
			} 
			
			$result = array(); 
			foreach($params as $queryKey => $query){ 
				if(sizeof($bindParams)){
					foreach($bindParams as $paramKey => $value){ 
						$bindParams[$paramKey] = $query[$paramKey]; 
					} 
				}
				$queryResult = array(); 
				if(mysqli_stmt_execute($stmt)){ 
					//echo "Executing";
					$resultMetaData = mysqli_stmt_result_metadata($stmt); 
					if($resultMetaData){                                                                               
						$stmtRow 	   = array();   
						$rowReferences = array(); 
						while ($field = mysqli_fetch_field($resultMetaData)) { 
							$rowReferences[] = &$stmtRow[$field->name]; 
						}                                
						mysqli_free_result($resultMetaData); 
						$bindResultMethod = new ReflectionMethod('mysqli_stmt', 'bind_result'); 
						$bindResultMethod->invokeArgs($stmt, $rowReferences); 
						while(mysqli_stmt_fetch($stmt)){ 
							$row = array(); 
							foreach($stmtRow as $key => $value){ 
								$row[$key] = $value;           
							} 
							$queryResult[] = $row; 
						} 
						mysqli_stmt_free_result($stmt); 
					} else { 
						$queryResult[] = mysqli_stmt_affected_rows($stmt); 
					} 
				} else {
					return "Error: ".mysqli_stmt_error($stmt); 
				} 
				$result[$queryKey] = $queryResult; 
			} 
			mysqli_stmt_close($stmt);   
		} else { 
			//$result = FALSE; 
			return "Error Preparing statment: ". mysqli_error($mysqli);
		} 

		if($mysqli->insert_id != ''){
			$result[0]['insert_id']= $mysqli->insert_id;	
		}else if($mysqli->affected_rows > 0)
			$result[0]['affected_rows']= $mysqli->affected_rows;
		//error_log("The count is:  ".count($result[0])." the res is: ". print_r($result,1));
		if(count($result[0])==0){
			return array();
		}else
			return $result[0]; 
		
	} 

?>