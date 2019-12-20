<?php

namespace Bitrix\Sale\Exchange;


use Bitrix\Crm\History\InvoiceStatusHistoryEntry;
use Bitrix\Crm\Statistics\InvoiceSumStatisticEntry;
use Bitrix\Main\Error;
use Bitrix\Sale\Exchange;
use Bitrix\Sale\Exchange\Entity\OrderImport;
use Bitrix\Sale\Result;

class ImportOneCPackageCRM extends ImportOneCPackage
{
	protected function modifyEntity($item)
	{
		$result = new Result();

		if($item instanceof OrderImport)
		{
			if($item->getId()>0)
			{
				$traits = $item->getField('TRAITS');

				$invoice = new \CCrmInvoice(false);
				if (!$invoice->SetStatus($item->getId(), $traits['STATUS_ID']))
				{
					$result->addError(new Error('Status error!'));
				}
			}
		}

		if($result->isSuccess())
			$result = parent::modifyEntity($item);


		return $result;
	}

	protected function save(Exchange\Entity\OrderImport $orderImport, $items)
	{
		$isNew = !($orderImport->getId()>0);

		$result = parent::save($orderImport, $items);

		if($result->isSuccess())
		{
			if($orderImport->getId()>0)
			{
				InvoiceStatusHistoryEntry::register($orderImport->getId(), null, array('IS_NEW' => $isNew));
				InvoiceSumStatisticEntry::register($orderImport->getId(), null);
			}
		}
		return $result;
	}
}