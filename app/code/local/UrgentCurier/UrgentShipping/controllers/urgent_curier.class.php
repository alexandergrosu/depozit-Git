<?php
class urgent_curier {
    function CheckClientCredentials($soap, $IdClient, $IdTarifSet, $IdUser){
        /*
        $client = new SoapClient($soap);
        $request = $client->CheckClientCredentials(
            array(
                'IdClient'=>$IdClient,
                'IdTarifSet'=>$IdTarifSet,
                'IdUser'=>$IdUser
            )
        );
        $result = $request->CheckClientCredentialsResult;
        return $result;
        */
        return true;
    }
    function GetPickUpSite($soap, $PickupSiteId) {
        $client = new SoapClient($soap);
        $request = $client->GetPickUpSite(
            array(
                'PickupSiteId'=>$PickupSiteId
            )
        );
        $result = $request->GetPickUpSiteResult;
        return (array)$result;
    }
    function GetPickUpSites($soap, $IdClient) {
        $client = new SoapClient($soap);
        $request = $client->GetPickUpSites(
            array(
                'IdClient'=>$IdClient
            )
        );
        $result = $request->GetPickUpSitesResult;
        return $result;
    }
    function GetPickUpSites_Simple($soap, $IdClient) {
        $client = new SoapClient($soap);
        $request = $client->GetPickUpSites(
            array(
                'IdClient'=>$IdClient
            )
        );
        $result = $request->GetPickUpSitesResult;
        foreach ($result as $array) {
            $array = (array)$array;
            if (!$array['Name']) {
                foreach ($array as $items) {
                    $items = (array)$items;
                    $return[$items['IdPickUpSite']] = $items['Name'];
                }
            } else {
                $return[$array['IdPickUpSite']] = $array['Name'];
            }
        }
        return $return;
    }
    function SavePickUpSite($soap, $IdPickUpSite, $IdClient, $Name, $IdOras, $Oras, $Address, $Phone, $Contact, $IdStrada, $NumarStrada, $Strada, $Email, $IsActive) {
        $client = new SoapClient($soap);
        $request = $client->SavePickUpSite(
            array(
                'pus'=>array(
                    'IdPickUpSite'=>$IdPickUpSite,
                    'IdClient'=>$IdClient,
                    'Name'=>$Name,
                    'IdOras'=>$IdOras,
                    'Oras'=>$Oras,
                    'OrasResedinta'=>$Oras,
                    'Address'=>$Address,
                    'Phone'=>$Phone,
                    'Contact'=>$Contact,
                    'IdStrada'=>$IdStrada,
                    'NumarStrada'=>$NumarStrada,
                    'Strada'=>$Strada,
                    'Email'=>$Email,
                    'IsActive'=>$IsActive
                )
            )
        );
        $result = $request->SavePickUpSiteResult;
        return $result;
    }
    function NewOrder($soap, $userId, $idClient, $PickUpSiteId, $PickUpSiteName, $PickUpDateFrom, $PickUpDateUntil, $IdOrasExp, $AdresaExp, $Email) {
        $client = new SoapClient($soap);
        $request = $client->NewOrder(
            array(
                'userId'=>$userId,
                'idClient'=>$idClient,
                'ord'=>array(
                    'orderId'=>'',
                    'CreationDate'=>date('c'),
                    'OrdStatus'=>'Created',
                    'PickUpSiteId'=>$PickUpSiteId,
                    'PickUpSiteName'=>$PickUpSiteName,
                    'PickUpDateFrom'=>date('c', strtotime(date('d M Y '.$PickUpDateFrom.':00'))-10800),
                    'PickUpDateUntil'=>date('c', strtotime(date('d M Y '.$PickUpDateUntil.':00'))-10800),
                    'NoAwb'=>'',
                    'NoParcel'=>'',
                    'NoEnvelop'=>'',
                    'IdOrasExp'=>$IdOrasExp,
                    'AdresaExp'=>$AdresaExp,
                    'GreutateTotala'=>'',
                    'Email'=>$Email
                )
            )
        );
        $result = $request->NewOrderResult;
        return $result;
    }
    function GetCurrentOrder($soap, $idClient, $idClientPunctLucru) {
        $client = new SoapClient($soap);
        $request = $client->GetCurrentOrder(
            array(
                'idClient'=>$idClient,
                'idClientPunctLucru'=>$idClientPunctLucru
            )
        );
        $result = (array)$request->GetCurrentOrderResult;
        return $result;
    }
    function GetAwbForComanda($soap, $orderId, $seed, $rank, $OrderType, $OrderDirection) {
        $client = new SoapClient($soap);
        $request = $client->GetAwbForComanda(
            array(
                'idComanda'=>$orderId,
                'seed'=>$seed,
                'rank'=>$rank,
                'OrderType'=>$OrderType,
                'OrderDirection'=>$OrderDirection
            )
        );
        $result = (array)$request->GetAwbForComandaResult->AwbSite;
        if ($result['IdAwbSite']) $output[] = $result; else $output = $result;
        return $output;
    }
    function NewAwb($soap, $idClient, $orderId, $SerieClient, $IdClientExp, $ClientExp, $IdOrasExp, $PersContactExp, $AdresaExp, $TelefonExp, $IdStradaExp, $NrStradaExp, $ClientDest, $IdOrasDest, $PersContactDest, $AdresaDest, $TelefonDest, $IdStradaDest, $NrStradaDest, $Ramburs, $IdTipPlatitorRamburs, $IdTarifSet, $ValoareDeclarata, $Plic, $Colet, $Greutate, $TipCerere, $RambursValoare, $IdTipPlatitor, $LivrareSambata, $AwbContent, $AwbObs, $Email) {
        $client = new SoapClient($soap);
        $request = $client->NewAwb(
            array(
                'IdClient'=>$idClient,
                'awb'=>array(
                    'IdAwbSite'=>'',
                    'OrderId'=>$orderId,
                    'DataCreare'=>date('c'),
                    'CodBara'=>'',
                    'SerieClient'=>$SerieClient,
                    
                    'IdClientExp'=>$IdClientExp,
                    'ClientExp'=>$ClientExp,
                    'IdOrasExp'=>$IdOrasExp,
                    'IdOrasResedintaExp'=>'',
                    'IdJudetExp'=>'',
                    'PersContactExp'=>$PersContactExp,
                    'AdresaExp'=>$AdresaExp,
                    'TelefonExp'=>$TelefonExp,
                    'IdStradaExp'=>$IdStradaExp,
                    'NrStradaExp'=>$NrStradaExp,
                    
                    'IdClientDest'=>'',
                    'ClientDest'=>$ClientDest,
                    'IdOrasDest'=>$IdOrasDest,
                    'IdOrasResedintaDest'=>'',
                    'IdJudetDest'=>'',
                    'PersContactDest'=>$PersContactDest,
                    'AdresaDest'=>$AdresaDest,
                    'TelefonDest'=>$TelefonDest,
                    'IdStradaDest'=>$IdStradaDest,
                    'NrStradaDest'=>$NrStradaDest,
                    
                    'OrasExp'=>'',
                    'OrasDest'=>'',
                    'OrasResedintaExp'=>'',
                    'OrasResedintaDest'=>'',
                    'StradaExp'=>'',
                    'StradaDest'=>'',
                    'CodPostalExp'=>'',
                    'CodPostalDest'=>'',
                    'TipStradaExp'=>'',
                    'TipStradaDest'=>'',
                    
                    'Deleted'=>false,
                    
                    'TransferedStatus'=>false,
                    'Ramburs'=>$Ramburs,
                    'IdTipPlatitorRamburs'=>$IdTipPlatitorRamburs,
                    
                    'AwbStatus'=>'Created',
                    'awbPriceSite'=>array(
                        'IdClient'=>$idClient,
                        'IdTarifSet'=>$IdTarifSet,
                        'IsLoco'=>false,
                        'DistantaExp'=>0,
                        'DistantaDest'=>0,
                        'ValoareDeclarata'=>$ValoareDeclarata,
                        'FondRisc'=>0,
                        'Plic'=>$Plic,
                        'Colet'=>$Colet,
                        'Greutate'=>$Greutate,
                        'ValoareKm'=>0,
                        'ValoareKg'=>0,
                        'ValoareDiscountata'=>0,
                        'Valoare'=>0,
                        'ValoareTVA'=>0,
                        'ValoareTotala'=>0,
                        'ValoareUrgenta'=>0,
                        'IdOrasExp'=>$IdOrasExp,
                        'IdOrasDest'=>$IdOrasDest,
                        'TipCerere'=>$TipCerere,
                        'RambursValoare'=>$RambursValoare,
                        'IdTipPlatitor'=>$IdTipPlatitor,
                        'TipPersoana'=>0,
                        'ValoareCostRamburs'=>0,
                        'TVACostRamburs'=>0,
                        'TotalCostRamburs'=>0,
                        'HasExpeditorTariff'=>false
                    ),
                    'LivrareSambata'=>$LivrareSambata,
                    'AwbContent'=>$AwbContent,
                    'AwbObs'=>$AwbObs,
                    'Email'=>$Email,
                    'StatusImagine'=>0
                )
            )
        );
        $result = $request->NewAwbResult;
        return $result;
    }
    function DeleteAwb($soap, $awbIds) {
        $client = new SoapClient($soap);
        $request = $client->DeleteAwb(
            array(
                'awbIds'=>array(
                    'int'=>$awbIds
                )
            )
        );
        $result = $request->DeleteAwbResult;
        return $result;
    }
    function GetAwbTracking($soap, $awbNumber, $idClient) {
        $client = new SoapClient($soap);
        $request = $client->GetAwbTracking(
            array(
                'awbNumber'=>$awbNumber,
                'idClient'=>$idClient
            )
        );
        $result = $request->GetAwbTrackingResult;
        return $result;
    }
    function GetAwbPrintEtichete($soap, $idClient, $CodBaras, $Printator) {
        $client = new SoapClient($soap);
        $request = $client->GetAwbPrintEtichete(
            array(
                'idClient'=>$idClient,
                'CodBaras'=>$CodBaras,
                'Printator'=>$Printator
            )
        );
        $result = $request->GetAwbPrintEticheteResult;
        return $result;
    }
    function GetAwbPrint($soap, $idClient, $CodBaras, $Printator) {
        $client = new SoapClient($soap);
        $request = $client->GetAwbPrint(
            array(
                'idClient'=>$idClient,
                'CodBaras'=>$CodBaras,
                'Printator'=>$Printator
            )
        );
        $result = $request->GetAwbPrintResult;
        return $result;
    }
    function GetBorderouPrint($soap, $idClient, $orderId, $Printator, $Receptioner) {
        $client = new SoapClient($soap);
        $request = $client->GetBorderouPrint(
            array(
                'idClient'=>$idClient,
                'orderId'=>$orderId,
                'Printator'=>$Printator,
                'Receptioner'=>$Receptioner
            )
        );
        $result = $request->GetBorderouPrintResult;
        return $result;
    }
    function GetOrders($soap, $IdClient, $Data1, $Data2, $Seed, $Rank, $OrderType, $OrderDirection, $IdClientPunctLucru) {
        $client = new SoapClient($soap);
        $request = $client->GetOrders(
            array(
                'IdClient'=>$IdClient,
                'Data1'=>$Data1,
                'Data2'=>$Data2,
                'Seed'=>$Seed,
                'Rank'=>$Rank,
                'OrderType'=>$OrderType,
                'OrderDirection'=>$OrderDirection,
                'IdClientPunctLucru'=>$IdClientPunctLucru
            )
        );
        $result = (array)$request->GetOrdersResult->Order;
        if ($result['orderId']) $output[] = $result; else $output = $result;
        return $output;
    }
    function EndOrder($soap, $userId, $idClient, $idClientPunctLucru, $OrdStatus, $PickUpDateFrom, $PickUpDateUntil) {
        $order = $this -> GetCurrentOrder($soap, $idClient, $idClientPunctLucru);
        $client = new SoapClient($soap);
        $request = $client->UpdateOrder(
            array(
                'userId'=>$userId,
                'idClient'=>$idClient,
                'ord'=>array(
                    'orderId'=>$order['orderId'],
                    'CreationDate'=>$order['CreationDate'],
                    'OrdStatus'=>$OrdStatus, // Created | Validated | Processed | Ordered | Taken | Revoked
                    'PickUpSiteId'=>$order['PickUpSiteId'],
                    'PickUpSiteName'=>$order['PickUpSiteName'],
                    'PickUpDateFrom'=>date('c', strtotime(date('d M Y '.$PickUpDateFrom.':00'))-10800),
                    'PickUpDateUntil'=>date('c', strtotime(date('d M Y '.$PickUpDateUntil.':00'))-10800),
                    'NoAwb'=>'',
                    'NoParcel'=>'',
                    'NoEnvelop'=>'',
                    'IdOrasExp'=>'',
                    'AdresaExp'=>$order['AdresaExp'],
                    'GreutateTotala'=>''
                )
            )
        );
        $result = $request->UpdateOrderResult;
        return $result;
    }
    function GetDetailsAwbById($soap, $IdClient, $idAwb){
        $client = new SoapClient($soap);
        $request = $client->GetDetailsAwbById(
            array(
                'idClient'=>$IdClient,
                'idAwb'=>$idAwb,
                'ExclusiveFromSite'=>false
            )
        );
        $result = (array)$request->GetDetailsAwbByIdResult;
        return $result;
    }
    function CalculateAwbSiteByValues($soap, $idClient, $idTarifSet, $Plic, $Colet, $Greutate, $OrasExp, $JudetExp, $OrasDest, $JudetDest, $ValoareDeclarata, $RambursCash, $RambursContColector){
        $client = new SoapClient($soap);
        $request = $client->CalculateAwbSiteByValues(
            array(
                'idClient'=>$idClient,
                'idTarifSet'=>$idTarifSet,
                'Plic'=>$Plic,
                'Colet'=>$Colet,
                'Greutate'=>$Greutate,
                'OrasExp'=>$OrasExp,
                'JudetExp'=>$JudetExp,
                'OrasDest'=>$OrasDest,
                'JudetDest'=>$JudetDest,
                'ValoareDeclarata'=>$ValoareDeclarata,
                'RambursCash'=>$RambursCash,
                'RambursContColector'=>$RambursContColector
            )
        );
        $result = $request->CalculateAwbSiteByValuesResult;
        return (array)$result;
    }
    function CalculateAwbSite($soap, $idClient, $idTarifSet, $ValoareDeclarata, $Plic, $Colet, $Greutate, $IdOrasExp, $IdOrasDest, $RambursValoare, $TipCerere){
        $client = new SoapClient($soap);
        $request = $client->CalculateAwbSite(
            array(
                'awbsp'=>array(
                    'IdClient'=>$idClient,
                    'IdTarifSet'=>$idTarifSet,
                    'IsLoco'=>'',
                    'DistantaExp'=>'',
                    'DistantaDest'=>'',
                    'ValoareDeclarata'=>$ValoareDeclarata,
                    'FondRisc'=>'',
                    'Plic'=>$Plic,
                    'Colet'=>$Colet,
                    'Greutate'=>$Greutate,
                    'ValoareKm'=>'',
                    'ValoareKg'=>'',
                    'ValoareDiscountata'=>'',
                    'Valoare'=>'',
                    'ValoareTVA'=>'',
                    'ValoareTotala'=>'',
                    'ValoareUrgenta'=>'',
                    'IdOrasExp'=>$IdOrasExp,
                    'IdOrasDest'=>$IdOrasDest,
                    'RambursValoare'=>$RambursValoare,
                    'TipCerere'=>$TipCerere,
                    'IdTipPlatitor'=>'',
                    'TipPersoana'=>'',
                    'ValoareCostRamburs'=>'',
                    'TVACostRamburs'=>'',
                    'TotalCostRamburs'=>'',
                    'HasExpeditorTariff'=>''
                )
            )
        );
        $result = $request->CalculateAwbSiteResult;
        return (array)$result;
    }
}
?>