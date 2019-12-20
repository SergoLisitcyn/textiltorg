<?
header("Content-Type: application/json; charset=utf-8");
define('STOP_STATISTICS', true);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

\Bitrix\Main\Loader::includeModule('iblock');

//imgGet(34);
imgReplace(34);

function imgGet($iblockID) {
	$dbEl = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $iblockID));

	while ($el = $dbEl->GetNext()) {
		$infoFIle = new SplFileInfo(CFile::GetPath($el['PREVIEW_PICTURE']));
		$fileExtension = $infoFIle->getExtension();


		CFile::CopyFile(
			$el['PREVIEW_PICTURE'],
			false,
			'img-optimazie/' . $el['ID'] . '.' . $fileExtension
		);

	}

}


function imgReplace($iblockID) {
	$dbEl = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $iblockID));

	while ($el = $dbEl->GetNext()) {
		$file = array();

		$infoFIle = new SplFileInfo(CFile::GetPath($el['PREVIEW_PICTURE']));
		$fileExtension = $infoFIle->getExtension();

		if ($fileExtension == 'png') {
			$dbFile = CFile::GetByID($el['PREVIEW_PICTURE']);
			$fileInfo = $dbFile->Fetch();


			$file['NAME'] = strtolower(trimToDot($fileInfo['ORIGINAL_NAME'])) . 'jpg';
			$file['DESCRIPTION'] = $fileInfo['DESCRIPTION'];
			$file['ELEMENT_ID'] = $el['ID'];


			$newFile = CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'] . '/upload/img-optimazie/' . $el['ID'] . '.jpg');

			$newFile['description'] = $file['DESCRIPTION'];


			$sliderEl = new CIBlockElement;


			global $USER;
			$sliderElFields = array(
				"MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
				"PREVIEW_PICTURE" => $newFile
			);
			print_r($sliderElFields);
			$sliderEl->Update($el['ID'], $sliderElFields);
			print_r($el['NAME']);
		}

	}

}

function trimToDot($string) {
	$pattern = '/.+?[\?|\!|\.]/';
	$result = array();
	if (preg_match($pattern, $string, $result) && !empty($result[0])){
		return $result[0];
	}else{
		return $string;
	}
}


require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');