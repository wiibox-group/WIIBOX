<?php
/**
 * Cgminer API
 *
 * @author wengebin
 * @package framework
 * @date 2014-04-15
 */
class CCgminerApi extends CApplicationComponents 
{

	/**
	 * 获得SOCKET连接
	 *
	 * @param string $_strAddr SOCKET连接地址
	 * @param string $_strPort SOCKET连接端口
	 * @return object
	 */
	public function getsock( $_strAddr = '' , $_strPort = '' )
	{
		$socket = null;
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($socket === false || $socket === null)
		{
			$error = socket_strerror(socket_last_error());
			return null;
		}

		$res = socket_connect($socket, $_strAddr, $_strPort);
		if ($res === false)
		{
			$error = socket_strerror(socket_last_error());
			socket_close($socket);
			return null;
		}
		return $socket;
	}

	/**
	 * 读取 SOCKET 数据
	 *
	 * @param object $_objSocket SOCKET连接对象
	 * @return string
	 */
	public function readsockline( $_objSocket = null )
	{
		if ( empty( $_objSocket ) )
			return '';

		$line = '';
		while (true)
		{
			$byte = socket_read($_objSocket, 1);
			if ($byte === false || $byte === '')
				break;
			if ($byte === "\0")
				break;
			$line .= $byte;
		}

		return $line;
	}

	/**
	 * 通过 SOCKET 发送命令
	 *
	 * @param string $_strCmd CMD命令
	 * @return array
	 */
	public function request( $_strCmd = '' )
	{
		$socket = self::getsock( '127.0.0.1' , 4028 );

		if ($socket != null)
		{
			socket_write( $socket, $_strCmd, strlen( $_strCmd ) );
			$line = self::readsockline($socket);
			socket_close($socket);

			if (strlen($line) == 0)
				return array();

			if (substr($line,0,1) == '{')
				return json_decode($line, true);

			$data = array();

			$objs = explode('|', $line);
			foreach ($objs as $obj)
			{
				if (strlen($obj) > 0)
				{
					$items = explode(',', $obj);
					$item = $items[0];
					$id = explode('=', $items[0], 2);
					if (count($id) == 1 or !ctype_digit($id[1]))
						$name = $id[0];
					else
						$name = $id[0].$id[1];

					if (strlen($name) == 0)
						$name = 'null';

					if (isset($data[$name]))
					{
						$num = 1;
						while (isset($data[$name.$num]))
							$num++;
						$name .= $num;
					}

					$counter = 0;
					foreach ($items as $item)
					{
						$id = explode('=', $item, 2);
						if (count($id) == 2)
							$data[$name][$id[0]] = $id[1];
						else
							$data[$name][$counter] = $id[0];

						$counter++;
					}
				}
			}

			return $data;
		}

		return array();
	}

//end class
}
