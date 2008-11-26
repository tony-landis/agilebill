<?
class hInfo
{
	var $RRADRetrieveCommand;
	var $RRADUpdateCommand;
	var $current_index = 0;
	var $RawElements;
	var $fields;
	var $numEls = 0;

	function getNumEls()
	{
		return $this->numEls;
	}

	function set($prop_name, $prop_val)
	{
		if (($this->numEls == 1) && ($this->current_index == 0))
			$this->next();
		$this->properties[$prop_name] = $prop_val;
		return true;	
	}

	function get($prop_name)
	{
		if (($this->numEls == 1) && ($this->current_index == 0))
			$this->next();
		return $this->properties[$prop_name];
	}

	function propertyExists($prop_name)
	{
		return isset($this->properties[$prop_name]);
	}

	function getRRADRetrieveCommand()
	{
		return $this->RRADRetrieveCommand;
	}

	function addElement($row)
	{
		$this->RawElements[] = $row;
		$this->numEls++;
	}

	function next()
	{
		// Use fields and replace properties
		// table with new values.
		if (   ($this->current_index < $this->numEls)
			&& ($this->current_index >= 0)  )
		{
			$currow = $this->RawElements[$this->current_index];
			for ($i=0; $i<sizeof($this->fields); $i++)
			{
				$fn = $this->fields[$i];
				$this->properties[$fn] = $currow[$i] ;
			}
			$this->current_index++;
			return true;
		}
		return false;
	}
}
?>
