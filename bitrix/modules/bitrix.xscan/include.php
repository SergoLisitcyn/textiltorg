<?
IncludeModuleLangFile(__FILE__);

Class CBitrixXscan 
{
	function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
	{
		if($GLOBALS['APPLICATION']->GetGroupRight("main") < "R")
			return;

		$MODULE_ID = basename(dirname(__FILE__));
		$aMenu = array(
			//"parent_menu" => "global_menu_services",
			"parent_menu" => "global_menu_settings",
			"section" => $MODULE_ID,
			"sort" => 50,
			"text" => $MODULE_ID,
			"title" => '',
//			"url" => "partner_modules.php?module=".$MODULE_ID,
			"icon" => "",
			"page_icon" => "",
			"items_id" => $MODULE_ID."_items",
			"more_url" => array(),
			"items" => array()
		);

		if (file_exists($path = dirname(__FILE__).'/admin'))
		{
			if ($dir = opendir($path))
			{
				$arFiles = array();

				while(false !== $item = readdir($dir))
				{
					if (in_array($item,array('.','..','menu.php')))
						continue;

					if (!file_exists($file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.$MODULE_ID.'_'.$item))
						file_put_contents($file,'<'.'? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/'.$MODULE_ID.'/admin/'.$item.'");?'.'>');

					$arFiles[] = $item;
				}

				sort($arFiles);

				foreach($arFiles as $item)
					$aMenu['items'][] = array(
						'text' => GetMessage("BITRIX_XSCAN_SEARCH"),
						'url' => $MODULE_ID.'_'.$item,
						'module_id' => $MODULE_ID,
						"title" => "",
					);
			}
		}
		$aModuleMenu[] = $aMenu;
	}

	function CheckFile($f)
	{
		static $spaces = "[ \r\t\n]*";
		static $var = '\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*';
		global $LAST_REG;

		static $me;
		if (!$me)
			$me = realpath(__FILE__);
		if (realpath($f) == $me)
			return false;
		# CODE 100
		if (basename($f) == '.htaccess')
		{
			$str = file_get_contents($f);
			$res = preg_match('#<(\?|script)#i',$str,$regs);
			$LAST_REG = $regs[0];
			return $res ? '[100] htaccess' : false;
		}

		# CODE 110
		if (preg_match('#^/upload/.*\.php$#',str_replace($_SERVER['DOCUMENT_ROOT'], '' ,$f)))
		{
			return '[110] php file in upload dir';
		}

		if (!preg_match('#\.php$#',$f,$regs))
			return false;

		# CODE 200
		if (false === $str = file_get_contents($f))
			return '[200] read error';

		# CODE 300
		if (preg_match('#[^a-z:](eval|assert|create_function|ob_start)'.$spaces.'\(([^\)]*)\)#i', $str, $regs))
		{
			$LAST_REG = $regs[0];
			if (preg_match('#\$(_COOKIE|_GET|_POST|_REQUEST|[a-z_]{2,}[0-9]+)#', $regs[2],$regs))
			{
				if (!self::LooksLike($f, '/bitrix/modules/main/classes/general/update_class.php') && $regs[1] != 'str_fill_path_value_2')
					return '[300] eval';
			}
		}

		# CODE 400
		if (preg_match('#\$(USER|GLOBALS..USER..)->Authorize'.$spaces.'\([0-9]+\)#i', $str, $regs))
		{
			$LAST_REG = $regs[0];
			if (!self::LooksLike($f, array('/bitrix/modules/bxtest/tests/security/filter/.base.php', '/bitrix/modules/bxtest/tests/tasks/classes/ctaskitem/.01_longtime_random_test.php', '/bitrix/modules/bxtest/tests/tasks/classes/ctaskitem/.bootstrap.php', '/bitrix/modules/main/install/install.php','/bitrix/modules/dav/classes/general/principal.php','/bitrix/activities/bitrix/controllerremoteiblockactivity/controllerremoteiblockactivity.php','/bitrix/modules/controller/install/activities/bitrix/controllerremoteiblockactivity/controllerremoteiblockactivity.php')))
				return '[400] bitrix auth';
		}

		# CODE 500
		if (preg_match('#[\'"]php://filter#i', $str, $regs))
		{
			$LAST_REG = $regs[0];
			if (!self::LooksLike($f, array('/bitrix/modules/bxtest/tests/main/classes/cfile/02_make_file_array.php')))
				return '[500] php wrapper';
		}

		# CODE 600
		if (preg_match('#(include|require)(_once)?'.$spaces.'\([^\)]+\.([a-z0-9]+).'.$spaces.'\)#i', $str, $regs))
		{
			$LAST_REG = $regs[0];
			if ($regs[3] != 'php')
				return '[600] strange include';
		}

		# CODE 610
		if (preg_match('#\$__+[^a-z_]#i', $str, $regs))
		{
			$LAST_REG = $regs[0];
			return '[610] strange vars';
		}

		# CODE 620
		if (preg_match('#\$['."_\x80-\xff".']+'.$spaces.'=#i', $str, $regs))
		{
			$LAST_REG = $regs[0];
			return '[620] binary vars';
		}

		# CODE 630
		if (preg_match('#[a-z0-9+=/]{255,}#i', $str, $regs))
		{
			$LAST_REG = $regs[0];
			if (!preg_match('#data:image/[^;]+;base64,[a-z0-9+=/]{255,}#i', $str, $regs))
			{
				if (!preg_match('#\$ser_content = \'#',$str))
					return '[630] long line';
			}
		}

		# CODE 640
		if (preg_match('#exif_read_data\(#i', $str, $regs))
		{
			$LAST_REG = $regs[0];
			if (!self::LooksLike($f, array('/bitrix/modules/main/classes/general/file.php')))
				return '[640] strange exif';
		}

		# CODE 650
		if (preg_match('#'.$var.$spaces.'\('.$spaces.'"(\\\\x([a-f0-9]{2}|[0-9]{3}))#i', $str, $regs))
		{
			$LAST_REG = $regs[0];
			return '[650] variable as function';
		}

		# CODE 660
		if (preg_match('#\$[a-z0-9]+\[[\'"][a-z0-9]+[\'"]][\[\]0-9]+\]\(#i', $str, $regs))
		{
			$LAST_REG = $regs[0];
			return '[660] array member as function';
		}

		# CODE 700
		if (preg_match('#file_get_contents\(\$[^\)]+\);[^a-z]*file_put_contents#mi', $str, $regs))
		{
			$LAST_REG = $regs[0];
			if (!self::LooksLike($f, array('/bitrix/components/bitrix/extranet.group_create/component.php', '/bitrix/components/bitrix/webdav/templates/.default/bitrix/bizproc.document/webdav.bizproc.document/template.php')))
				return '[700] file from variable';
		}

		# CODE 710
		if (preg_match('#file_get_contents\([\'"]https?://#mi', $str, $regs))
		{
			$LAST_REG = $regs[0];
			return '[710] file from the Internet';
		}

		# CODE 640
		if (preg_match("#[\x01-\x08\x0b\x0c\x0f-\x1f]#", $str, $regs))
		{
			$LAST_REG = $regs[0];
			if (!preg_match('#\$ser_content = \'#',$str))
				return '[640] binary data';
		}

		# CODE 800
		if (preg_match('#preg_replace\(\$_#mi', $str, $regs))
		{
			$LAST_REG = $regs[0];
			return '[800] preg_replace pattern from variable';
		}

		# CODE 670
		if (preg_match('#('.$var.')'.$spaces.'\('.$spaces.$var.'#i', $str, $regs))
		{
			$LAST_REG = $regs[0];
			$src_var = $regs[1];
			while(preg_match('#\$'.str_replace('$', '', $src_var).$spaces.'='.$spaces.'('.$var.')#i', $str, $regs))
			{
				$src_var = str_replace('$', '', $regs[1]);
			}
			if (preg_match('#^_(COOKIE|GET|POST|REQUEST)$#', $src_var))
				return '[670] function from global var';
		}

		# CODE END
		return false;
	}


	function Search($path)
	{
		$path = str_replace('\\','/',$path);
		do 
		{
			$path = str_replace('//', '/', $path, $cnt);
		} 
		while($cnt);

		if (time() - START_TIME > 10)
		{
			if (!defined('BREAK_POINT'))
				define('BREAK_POINT', $path);
			return;
		}

		if (defined('SKIP_PATH') && !defined('FOUND')) // проверим, годится ли текущий путь
		{
			if (0 !== self::bin_strpos(SKIP_PATH, dirname($path))) // отбрасываем имя или идём ниже 
				return;

			if (SKIP_PATH==$path) // путь найден, продолжаем искать текст
				define('FOUND',true);
		}

		if (is_dir($path)) // dir
		{
			$p = realpath($path);
			if (strpos($p, $_SERVER['DOCUMENT_ROOT'].'/bitrix/cache') === 0
			|| strpos($p, $_SERVER['DOCUMENT_ROOT'].'/bitrix/managed_cache') === 0
			|| strpos($p, $_SERVER['DOCUMENT_ROOT'].'/bitrix/stack_cahe') === 0
			)
				return;

			if (is_link($path))
			{
				$d = dirname($path);
				if (strpos($p, $d) !== false || strpos($d, $p) !== false) // если симлинк ведет на папку внутри структуры сайта или на папку выше
					return true;
			}

			$dir = opendir($path);
			while($item = readdir($dir))
			{
				if ($item == '.' || $item == '..')
					continue;

				self::Search($path.'/'.$item);
			}
			closedir($dir);
		}
		else // file
		{
			if (!defined('SKIP_PATH') || defined('FOUND'))
				if ($res = self::CheckFile($path))
					self::Mark($path, $res);
		}
	}

	function LooksLike($f, $mask)
	{
		$f = str_replace('\\','/',$f);
		if (is_array($mask))
		{
			foreach($mask as $m)
			{
				if (preg_match('#'.$m.'$#',$f))
					return true;
			}
		}
		return preg_match('#'.$mask.'$#',$f);
	}

	function bin_strpos($s, $a)
	{
		if (function_exists('mb_orig_strpos'))
			return mb_orig_strpos($s, $a);
		return strpos($s, $a);
	}

	function Mark($f, $type)
	{
		if (false === file_put_contents(XSCAN_LOG, $f."\t".$type."\n", 8))
		{
			ShowError('Write error: '.XSCAN_LOG);
			die();
		}
	}

	function ShowMsg($str, $color = 'green')
	{
		CAdminMessage::ShowMessage(array(
			"MESSAGE" => '',
			"DETAILS" => $str,
			"TYPE" => $color == 'green' ? "OK" : 'ERROR',
			"HTML" => true));
	}

	function HumanSize($s)
	{
		$i = 0;
		$ar = array('b','kb','M','G');
		while($s > 1024)
		{
			$s /= 1024;
			$i++;
		}
		return round($s,1).' '.$ar[$i];
	}

	function CheckBadLog()
	{
		if (file_exists(XSCAN_LOG))
		{
			CBitrixXscan::ShowMsg(GetMessage("BITRIX_XSCAN_COMPLETED_FOUND"), 'red');
			echo GetMessage("BITRIX_XSCAN_DATA_IZMENENIA_JURNA").ConvertTimeStamp(filemtime(XSCAN_LOG), 'FULL');
			echo '<table width=80% border=1 style="border-collapse:collapse;border-color:#CCC">';
			echo '<tr>
				<th>'.GetMessage("BITRIX_XSCAN_NAME").'</th>
				<th>'.GetMessage("BITRIX_XSCAN_TYPE").'</th>
				<th>'.GetMessage("BITRIX_XSCAN_SIZE").'</th>
				<th>'.GetMessage("BITRIX_XSCAN_M_DATE").'</th>
				<th></th>
				</tr>';

			$ar = file(XSCAN_LOG);
			foreach($ar as $line)
			{
				list($f, $type) = explode("\t", $line);
				{
					$code = preg_match('#\[([0-9]+)\]#', $type, $regs) ? $regs[1] : 0;
					$fu = urlencode(trim($f));
					$bInPrison = strpos('[100]', $type) === false;

					if (!file_exists($f) && file_exists($new_f = preg_replace('#\.php$#', '.ph_', $f)))
					{
						$bInPrison = false;
						$f = $new_f;
						$fu = urlencode(trim($new_f));
					}

					echo '<tr>
						<td><a href="?action=showfile&file='.$fu.'" title="'.GetMessage("BITRIX_XSCAN_SRC").'" target=_blank>'.htmlspecialcharsbx(str_replace($_SERVER['DOCUMENT_ROOT'], '', $f)).'</a></td>
						<td>'.htmlspecialcharsbx($type).'</td>
						<td>'.CBitrixXscan::HumanSize(filesize($f)).'</td>
						<td>'.ConvertTimeStamp(filemtime($f), 'FULL').'</td>
						<td>'.($bInPrison ? '<a href="?action=prison&file='.$fu.'&'.bitrix_sessid_get().'" onclick="if(!confirm(\''.GetMessage("BITRIX_XSCAN_WARN").'\'))return false;" title="'.GetMessage("BITRIX_XSCAN_QUESTION").'">' : '').GetMessage("BITRIX_XSCAN_KARANTIN").'</a></td>
						</tr>';
				}
			}
			echo '</table>';
		}
	}
}
?>
