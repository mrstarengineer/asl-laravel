<?php
    $vehicle_weight = 0;
    foreach($export->vehicles as $vehicle) {
        $vehicle_weight += (int) $vehicle->weight;
    }
    $manifestDate = date('Y-m-d', strtotime($export->created_at));
?>
<style>
    .manifesta {
        /* width: 900px; */
        margin-right: auto;
        margin-left: auto;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 11px;
    }

    .manifesta table {
        margin-top: 5px;
    }

    #print_this {
        float: left;
        /*width: 800px;*/
    }

    .invoice-view {
        background: #fff;
        padding: 30px;
    }

    .manifesta #print_this .top_processes.ui-state-highlight {
        padding: 4px;
        width: 890px;
        float: left;
    }

    #print_this .top_processes.ui-state-highlight #print {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 9px;
    }

    .lefti {
        float: left;
    }

    .manifesta .ui-state-active,
    .manifesta .ui-widget-content .ui-state-active,
    .manifesta .ui-widget-header .ui-state-active {
        background-color: #0aa89e !important;
        border-color: #0aa89e;
        font-weight: normal;
        color: #ffffff;
    }

    .manifesta .ui-widget-header {
        background-color: #caf0ee;
        border-color: #caf0ee;
        /* color: #eaf5f7; */
        color: #515151;
        font-weight: bold;
    }

    .manifesta #consi {
        margin-left: 10px;
        margin-top: 2px;
    }

    .manifesta #olpa {
        border: 1px solid #000;
    }

    #btnmanifest tr td {
        padding-left: 5px !important;
    }

    .manifesta #olpa1,
    #olpa {
        border: 1px solid #000;
        width: 97%;
        height: 100px;
    }

    .manifesta #shipi {
        margin-top: 2px;
        margin-right: 8px;
    }

    .manifesta .line_under {
        border-bottom-width: 1px;
        border-bottom-style: solid;
        border-bottom-color: #000;
    }

    #print_this .top_processes.ui-state-highlight .filer {
        float: right;
    }

    .manifesta .lefti.ui-widget-header tr #lli {
        font-size: 16px;
        margin-left: 10px;
        font-weight: bold;
    }

    span {
        font-size: 9px;
    }

    th {
        text-align: left;
    }
    td {
        height: 14px !important;
    }
</style>
<div id="btnmanifest" class="condition_reports menifest_modal_print" contenteditable="true">
    <div class="cond_here">
        <div class="manifesta">
            <table class=" ui-widget-header" width="100%" contenteditable="false">
                <tbody><tr>
                    <td width="70%" rowspan="2" id="lli">{{ env('APP_NAME') }} MANIFEST</td>
                    <td width="12%">Manifest #:</td><td width="18%"></td>
                </tr>
                <?php if(!empty($export->ar_number)){ ?>
                <tr>
                    <td colspan=3>
                        @php echo '<img width="254" height="20" src="data:image/png;base64,' . DNS1D::getBarcodePNG($export->ar_number, 'C128') . '" alt="barcode"   />'; @endphp
                    </td>
                </tr>
                <?php } ?>
                <tr><td>ETA:</td><td><?php echo $export->eta; ?></td></tr>
                </tbody></table>

            <table width= "100%">
                <tr>
                    <td  width="50%" style="border: 1px solid #000;background-color: #d9fbfa;">
                        <table id="shipi" class="lefti" width="100%" style="float:left;">
                            <thead><tr class="ui-widget-header"><td><b>Shipper</b></td></tr></thead>
                        </table>
                    </td>
                    <td  width="50%" style="border: 1px solid #000;background-color: #d9fbfa;">
                        <table id="shipi" class="lefti" width="100%" style="float:left;">
                            <thead><tr class="ui-widget-header"><td><b>Notify / Consignee</b></td></tr></thead>
                        </table>
                    </td>

                </tr>
            </table>

            <table width="100%">
                <tr>
                    <td width="50%">
                        <table  class="" id="olpa">
                            <tbody>
                            <?php if($export->exporter){ ?>
                            <tr><td>{{ $export->exporter->company_name . ' ' . $export->exporter->address_line_1 }}</td></tr>
                            <tr><td>{{ data_get($export, 'exporter.state.name') }} </td></tr>
                            <tr><td>{{ data_get($export, 'exporter.city.name') . ' ' . data_get($export, 'exporter.zip_code') }}</td></tr>
                            <tr><td> {{ $export->exporter->phone }}</td></tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </td>
                    <td width="50%">
                        <table id="olpa" class="manifest_second_table" >
                            <tbody>
                            <?php $consignee_id = data_get($export, 'houstan_custom_cover_letter.notify_party') ?>
                            <?php
                                $consignee = data_get($export, 'houstan_custom_cover_letter.notify_party_item');
                            if ($consignee) {
                            ?>
                            <tr><td colspan="4">{!! $consignee->consignee_name . '<br>' . $consignee->consignee_address_1 !!}</td></tr>
                            <tr>
                                <td colspan="4">PHONE :<span class="inputtext">{{ $consignee->phone }}</span></td>
                            </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
            <table class=" line_under" width="100%">
                <tbody><tr class="ui-widget-header"><th>Description</th></tr>
                <tr>
                    <td>{{ $export->special_instruction }}</td>
                </tr>
                </tbody>
            </table>

            <table class="" width="100%">
                <tbody>
                <tr>
                    <td class="ui-state-active" width="17%"><b>Vessel/Voyage:</b></td><td width="28%">{{ $export->vessel . ' / ' . $export->voyage }}</td>
                    <td width="16%"></td>
                    <td width="21%"></td>
                    <td align="center" width="18%" class="ui-state-active"><b>Weight</b></td>
                </tr>
                <tr>
                    <td class="ui-state-active">
                        <b>Cut Off:</b>
                    </td>
                    <td>{{ $export->cutt_off }}</td><td></td>
                    <td></td><td align="center" class="line_under">{{ $vehicle_weight }}</td>
                </tr>
                <tr>
                    <td class="ui-state-active"><b>Booking#:</b></td>
                    <td>{{ $export->booking_number }}</td><td></td>
                    <td></td>
                    <td align="center" class="ui-state-active"><b>Pieces</b></td>
                </tr>
                <tr><td class="ui-state-active"><b>File Ref#:</b></td><td>{{ $export->ar_number }}</td><td></td><td></td><td align="center" class="line_under">{{ $export->vehicles->count() }}</td></tr>
                <tr><td class="ui-state-active"><b>Container#:</b></td><td>{{ $export->container_number }}</td><td>&nbsp;</td><td></td><td></td></tr>
                <tr><td class="ui-state-active"><b>Seal#:</b></td><td>{{ $export->seal_number }}</td><td>&nbsp;</td><td></td><td></td></tr>
                <tr>
                    <td class="ui-state-active"><b>Export Terminal:</b></td><td>{{ $export->terminal }}</td>
                    <td class="ui-state-active" style="min-width: 175px;">Export Date: {{ $export->export_date }}</td>
                    <td></td>
                    <td class="ui-state-active">Manifest Date: {{ $manifestDate }}</td>
                </tr>
                </tbody>
            </table>

            <table width="100%" class="">
                <thead>
                <tr class="ui-state-active">
                    <th width="6%">Year</th>
                    <th>Make</th>
                    <th>Model</th>
                    <th width="17%">VIN</th>
                    <th width="5%">Hat #</th>
                    <th width="6%">Towing</th>
                    <th width="7%">Auction Storage</th>
                    <th width="5%">Title Fee</th>
                    <th width="7%">Storage Days</th>
                    <th width="9%">Arrival Date</th>
                    <th width="4%">Add. Ch.</th>
                    <th width="5%">Yard Storage</th>
                    <th width="5%">Keys</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $towwed = 0;
                $storage = 0;
                $add_chg = 0;
                $arinastog = 0;
                $titlefeetotal= 0;
                foreach ($export->vehicles as $vehicle_detail) {
                    $towing_request = $vehicle_detail->towing_request;
                    $deliverDate = $towing_request->deliver_date;
                    $storageDays = $deliverDate ? round( ( strtotime($manifestDate) - strtotime( $deliverDate ) ) / ( 60 * 60 * 24 ) ) : 0;
                    $storageDays = $storageDays > 30 ? $storageDays - 30 : 0;
                ?>
                <tr>
                    <td align="center">{{ $vehicle_detail->year }}</td>
                    <td align="center" style="border-left: 1px solid black;">{{ $vehicle_detail->make }}</td>
                    <td align="center" style="border-left: 1px solid black;">{{ $vehicle_detail->model }}</td>
                    <td align="center" style="border-left: 1px solid black;">{{ $vehicle_detail->vin }}</td>
                    <td align="center" style="border-left: 1px solid black;">{{ $vehicle_detail->hat_number }}</td>
                    <td align="center" style="border-left: 1px solid black;">{{ (int) $vehicle_detail->towed_amount }}</td>
                    <td align="center" style="border-left: 1px solid black;">{{ (int) $vehicle_detail->storage_amount }}</td>
                    <td align="center" style="border-left: 1px solid black;">{{ $vehicle_detail->title_amount }}</td>
                    <td align="center" style="border-left: 1px solid black;"><?php echo  $storageDays; ?></td>
                    <td align="center" style="border-left: 1px solid black;"><?php echo $towing_request->deliver_date; ?></td>
                    <td align="center" style="border-left: 1px solid black;">{{ $vehicle_detail->additional_charges }}</td>
                    <td align="center" style="border-left: 1px solid black;"><?php echo $storageDays * 5 ?></td>
                    <?php
                    if ($vehicle_detail->status != '4' && $towing_request['deliver_date'] && date('Y-m-d') > $towing_request['deliver_date']) {

                        if($export->loading_date){
                            $current_date = $export->loading_date;
                        }else{
                            $current_date = date('Y-m-d');
                        }
                        $datediff = strtotime($current_date) - strtotime($towing_request['deliver_date']);
                        $days = floor($datediff / (60 * 60 * 24));
                        if ($days > 30) {
                            $days = $days - 30;
                            // echo $days = $days * 5;
                            $arinastog = $arinastog + $days;
                        } else {
                            // echo $days = '0';
                        }
                    } else {
                        // echo $days = '0';
                    }
                    ?>
                    <td align="center" style="border-left: 1px solid black;"><?php if ($vehicle_detail->keys) {echo 'Yes';} else {echo 'No';}?></td>
                </tr>
{{--                <tr>--}}
{{--                    <th></th>--}}
{{--                    <th></th>--}}
{{--                    <th colspan="3" style="text-align:center">--}}
{{--                        @php echo '<img width="254" height="20" src="data:image/png;base64,' . DNS1D::getBarcodePNG($vehicle_detail->vin, 'C128') . '" alt="barcode"   />'; @endphp--}}
{{--                    </th>--}}
{{--                </tr>--}}
                <?php
                $towwed = $towwed + floatval($vehicle_detail->towed_amount);
                $storage = $storage + floatval($vehicle_detail->storage_amount);
                $add_chg = $add_chg + floatval($vehicle_detail->additional_charges);
                $titlefeetotal += floatval($vehicle_detail->title_amount);
                }
                ?>

                <tr><td class="ui-state-highlight" align="right" colspan="5"><b>Total:</b></td>
                    <td align="center" class="ui-state-active" width="6%">{{ $towwed }}</td>
                    <td align="center" class="ui-state-active" width="6%">{{ $storage }}</td>
                    <td class="ui-state-active" align="center" width="7%"></td>
                    <td class="ui-state-active"></td>
                    <td class="ui-state-active"></td>
                    <td class="ui-state-active" align="center" width="9%">{{ $add_chg }}</td>
                    <td width="8%" class="ui-state-active" align="center">{{ $titlefeetotal }}</td>
                    <td width="6%"></td>
                </tr>
                <tr>
                    <td colspan='11'>
                        VEHICLES ARE BRACED AND BLOCKED.FUEL TANKS HAVE BEEN SECURELY CLOSED.THE KEYS ARE NOT IN THE IGNITION.BATERIES ARE SECURED AND FASTENED IN THE UPRIGHT POSITION AND PROTECTED AGAINST SHORT CIRCUITS. THE FUEL TANKS ARE EMPTY AND THE ENGINE STOPPED DUE TO LACK OF FUEL.THE VEHICLES HAVE BEEN LOADED INTO THE CONTAINER IN RANCHO DOMINGUEZ, CALIFORNIA.
                    <td>
                </tr>
                </tbody>
            </table>
            <hr class="line_under" />
            <table class="" width="100%">
                <tbody>
                <tr><td width="23%">Received in Good Order</td><td width="34%" class="line_under"></td><td width="10%">Date/Time</td><td width="33%" class="line_under"><?php //date('Y-m-d h:i:s a', time());?></td></tr>
                <tr><td>Drivers Signature</td><td class="line_under"></td><td>Date/Time</td><td class="line_under"></td></tr>
                <tr><td>Shippers Signature</td><td class="line_under"></td><td>Date/Time</td><td class="line_under"></td></tr>
                </tbody>
            </table>
            <footer>
                <table width="100%">
                    <tr>
                        <td colspan='4'>
                            <br />
                            The liability of {{ env('APP_NAME') }}, for any reason shall in no case exceed $0.50 cent per hundred pounds or $500.00 per shipment whichever is less.
                            {{ env('APP_NAME') }} will not be liable for consequential or incidental damages or loss of profits. Net 15 days, with a monthly finance charge of 1.5% on all balances over thirty days.
                            {{ env('APP_NAME') }} reserves the right to hold or lien cargo for nonpayment Payment is required within (15) days of presentation.
                            Failure to pay billed charges may result in lien on future shipment, including cost of storage and appropriate security for the subsequent shipment(s) held pursuant to California Civil Code, Section 3051.5
                            <br/>
                            {{ env('APP_NAME') }} is a freight forwarding company, and we are not liable for any charges if your container is stopped by the US Customs for random, routine procedural checks.
                            <br />
                            On our end, we will always make sure to have all the necessary paperwork attached when we ship your container and take the correct steps to meet all requirements.  However, due to US Customs policy, they can always stop a container for random inspections.  Although we will try our best to help you with anything we can, we are not responsible for this stop or any fees related to it because they are a completely separate entity from us.  You will be liable to US Customs and all charges pertaining to this stop will be covered by you and paid directly to them.
                        </td>
                    </tr>
                </table>
            </footer>
        </div>
    </div>
</div>
