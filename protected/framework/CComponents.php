<?php
/**
 * CComponents class file.
 * 
 * 
 * @author samson.zhou	<samson.zhou@newbiiz.com>
 * @package framework
 * @date 2010-06-24
 */

class CComponents
{
	/**
	 * Returns a property value, an event handler list or a behavior based on its name.
	 * Do not call this method. This is a PHP magic method that we override
	 * to allow using the following syntax to read a property or obtain event handlers:
	 * <pre>
	 * $value=$component->propertyName;
	 * $handlers=$component->eventName;
	 * </pre>
	 * @param string the property name or event name
	 * @return mixed the property value, event handlers attached to the event, or the named behavior (since version 1.0.2)
	 * @throws CException if the property or event is not defined
	 * @see __set
	 */
	public function __get($name)
	{
		$getter='get'.$name;
		if(method_exists($this,$getter))
			return $this->$getter();
		throw new CException('Property "'.get_class($this).'.'.$name.'" is not defined.');
	}

	/**
	 * Sets value of a component property.
	 * Do not call this method. This is a PHP magic method that we override
	 * to allow using the following syntax to set a property or attach an event handler
	 * <pre>
	 * $this->propertyName=$value;
	 * $this->eventName=$callback;
	 * </pre>
	 * @param string the property name or the event name
	 * @param mixed the property value or callback
	 * @throws CException if the property/event is not defined or the property is read only.
	 * @see __get
	 */
	public function __set($name,$value)
	{
		$setter='set'.$name;		
		if(method_exists($this,$setter))
			return $this->$setter($value);
		if(method_exists($this,'get'.$name))
			throw new CException('Property "'.get_class($this).'.'.$name.'" is read only.');			
		else
			throw new CException('Property "'.get_class($this).'.'.$name.'" is not defined.');
	}

	/**
	 * Checks if a property value is null.
	 * Do not call this method. This is a PHP magic method that we override
	 * to allow using isset() to detect if a component property is set or not.
	 * @param string the property name or the event name
	 * @since 1.0.1
	 */
	public function __isset($name)
	{
		$getter='get'.$name;
		if(method_exists($this,$getter))
			return $this->$getter()!==null;
		else
			return false;
	}

	/**
	 * Sets a component property to be null.
	 * Do not call this method. This is a PHP magic method that we override
	 * to allow using unset() to set a component property to be null.
	 * @param string the property name or the event name
	 * @throws CException if the property is read only.
	 * @since 1.0.1
	 */
	public function __unset($name)
	{
		$setter='set'.$name;
		if(method_exists($this,$setter))
			$this->$setter(null);
		else if(method_exists($this,'get'.$name))
			throw new CException( "Property \"".get_class($this).".{$name}\" is read only." );			
	}

	/**
	 * Calls the named method which is not a class method.
	 * Do not call this method. This is a PHP magic method that we override
	 * to implement the behavior feature.
	 * @param string the method name
	 * @param array method parameters
	 * @return mixed the method return value
	 * @since 1.0.2
	 */
	public function __call($name,$parameters)
	{
		/*
		if($this->_m!==null)
		{
			foreach($this->_m as $object)
			{
				if($object->getEnabled() && method_exists($object,$name))
					return call_user_func_array(array($object,$name),$parameters);
			}
		}*/
		throw new CException( get_class($this)."does not have a method named \"{$name}\"" );
	}
	
//end class	
}