<style>
    .line_under {
        border-bottom-width: 1px;
        border-bottom-style: solid;
        border-bottom-color: #000;
        font-family: "Courier New", Courier, monospace;
    }

    #modal_content {
        /* width: 8in;
        height: 10.5in; */
        font-size: 12px;
    }

    #modal_content table,
    #modal_content table tr td {
        border: 1px solid black;
        border-collapse: collapse;
        vertical-align: top;
        padding: 1px;
        font-family: Arial, Helvetica, sans-serif;
    }
    #cars_no_border {
        border: 0px solid #000 !important;
    }
    .table_td_no_border td {
        border: 0px solid #000 !important;
    }
</style>

<div id="modal_content">
    <div class="cond_here">
        <div id="page_border" style="padding:15px;">
            <center><strong style="font-family:Arial, Helvetica, sans-serif; font-size:16px;">DOCK RECEIPT</strong>
            </center>
            <table width="100%" contenteditable="true">
                <tbody>
                <tr>
                    <td colspan="3" rowspan="2" width="50%" valign="top" style="height:100px;">
                        <i>2.EXPORTER (Principal or seller-license and address including ZIP Code)</i>
                        <br>
                        {{ data_get($export, 'exporter.company_name') . ' ' . data_get($export, 'exporter.address_line_1') }}
                        <br>
                        {{ data_get($export, 'exporter.state.name') }}
                        <br>
                        {{ data_get($export, 'exporter.zip_code') }}
                    </td>
                    <td colspan="2" valign="top" style="height:65px;"><i>5.BOOKING NUMBER</i>
                        <br><strong> {{ $export->booking_number }}</strong>
                    </td>
                    <td valign="top">
                        <i>5a.B/L OR AWB NUMBER</i><br>
                        {{ data_get($export, 'dock_receipt.awb_number') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <i>6.EXPORT REFERENCES</i><br>
                        {{ data_get($export, 'dock_receipt.export_reference') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="3" rowspan="2" style="height:90px;"><i>3.CONSIGNED TO</i>
                        <br>
                        {{ data_get($export, 'houstan_custom_cover_letter.consignee_item.consignee_name') }} <br>
                        {{ data_get($export, 'houstan_custom_cover_letter.consignee_item.consignee_address_1') }} <br>
                        {{ data_get($export, 'houstan_custom_cover_letter.consignee_item.customer.phone_two') }}
                    </td>
                    <td colspan="3" style="height:70px;">
                        <i>7.FORWARDING AGENT (NAME &amp; ADDRESS - REFERENCES)</i> <br>
                        {{ data_get($export, 'dock_receipt.forwarding_agent') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <i>8.POINT(STATE) OF ORIGIN OR FTZ NUMBER</i><br>
                        <strong>{{ $export->port_of_loading }}</strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="height:90px;">
                        <i>4.NOTIFY PARTY/INTERMEDIATE CONSIGNEE (Name and Address)</i><br>
                        {{ data_get($export, 'houstan_custom_cover_letter.notify_party.consignee_name'). ' '. data_get($export, 'houstan_custom_cover_letter.notify_party.consignee_address_1') }}
                        <br>
                        {{ data_get($export, 'houstan_custom_cover_letter.notify_party.consignee_address_2'). ' '. data_get($export, 'houstan_custom_cover_letter.notify_party.state.name') }}
                        <br>
                        {{ data_get($export, 'houstan_custom_cover_letter.notify_party.country.name'). ' '. data_get($export, 'houstan_custom_cover_letter.notify_party.zip_code') }}
                        <br>
                    </td>
                    <td colspan="3" rowspan="2" style="height:125px;">
                        <i>9.DOMESTIC ROUTING/EXPORT INSTRUCTIONS</i><br>
                        {{ data_get($export, 'dock_receipt.domestic_routing_instructions') }}<br>
                        <div>
                            AUTO RECEIVING DATE: {{ data_get($export, 'dock_receipt.auto_receiving_date') }} <br>
                            AUTO CUT OFF: {{ data_get($export, 'dock_receipt.auto_cut_off') }} <br>
                            VESSEL CUT OFF: {{ data_get($export, 'dock_receipt.vessel_cut_off') }} <br>
                            SAIL DATE: {{ data_get($export, 'dock_receipt.sale_date') }}
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" width="25%" style="height: 40px;">
                        <i>12.PRE-CARRIAGE BY</i><br>
                        {{ data_get($export, 'dock_receipt.pre_carriage_by') }}
                    </td>
                    <td width="25%">
                        <i style="font-size: 8px;">13.PLACE OF RECEIPT BY PRE-CARRIER</i><br>
                        {{ data_get($export, 'dock_receipt.place_of_receipt_by_pre_carrier') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="height: 40px;">
                        <i>14.EXPORTING CARRIER</i> <br>
                        {{ data_get($export, 'dock_receipt.exporting_carrier') }}
                    </td>
                    <td>
                        <i>15.PORT OF LOADING/EXPORT</i> <br>
                        {{ array_key_exists( data_get($export, 'port_of_loading', 0), trans( 'exports.port_of_loadings' ) ) ? trans( 'exports.port_of_loadings.' . data_get($export, 'port_of_loading') ) : '' }}
                    </td>
                    <td colspan="3">
                        <i>10.LOADING PIER/TERMINAL</i><br>
                        <strong>{{ data_get($export, 'dock_receipt.loading_terminal') }}</strong>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" rowspan="2" style="height: 40px;">
                        <i>16.FOREIGN PORT OF UNLOADING</i><br>
                        {{ array_key_exists( data_get($export, 'port_of_discharge', 0), trans( 'exports.port_of_loadings' ) ) ? trans( 'exports.port_of_discharges.' . data_get($export, 'port_of_discharge') ) : '' }}
                    </td>
                    <td rowspan="2">
                        <i>17.FINAL DESTINATION</i><br>
                        {{ data_get($export, 'dock_receipt.final_destination') }}
                    </td>
                    <td rowspan="2" width="30%">
                        <i>11.AES#</i><br>
                        {{ $export->itn }}
                    </td>
                    <td colspan="2">
                        <i>11a.CONTAINERIZED(VESSEL)</i>
                    </td>
                </tr>
                <tr>
                    <td width="15%"><i>{{ $export->vessel ? 'YES' : 'NO' }}</i></td>
                    <td><i>NO</i></td>
                </tr>
                <tr>
                    <td width="20%" style="height: 40px;"><i>MARKS &amp; NUMBERS</i></td>
                    <td width="5%"><i>NUMBER OF PACKAGES(19)</i></td>
                    <td colspan="2"><i>(20)DESCRIPTION OF COMMODITIES</i><br><strong>AUTOS</strong></td>
                    <td><i>GROSS WEIGHT<br>(LBS.)(21)</i></td>
                    <td><i>MEASUREMENT<br>(22)</i></td>
                </tr>
                <tr>
                    <td style="">
                        <strong>CONTAINER NO.:</strong><br>
                        <strong>{{ $export->container_number }}</strong>
                        <br>
                        <strong>SEAL#<br>{{ $export->seal_number }}</strong>
                    </td>
                    <td>{{ data_get($export, 'dock_receipt.number_of_packages') }}</td>
                    <td colspan="2">

                        <table id="cars_no_border">
                            <tbody>
                            <center>{{ trans('exports.container_types.'.$export->container_type) }}</center>
                            @php $vehicle_weight = 0; $i = 1; @endphp
                            @foreach ( $export->vehicles as $vehicle_detail )
                                @php $vehicle_weight += (int) $vehicle_detail->weight; @endphp
                                <tr class="table_td_no_border">
                                    <td align="center">{{ $i++ }}</td>
                                    <td align="center">{{ $vehicle_detail->year }}</td>
                                    <td align="center">{{ $vehicle_detail->make }}</td>
                                    <td align="center">{{ $vehicle_detail->model }}</td>
                                    <td align="center">{{ $vehicle_detail->vin }}</td>
                                    <td align="center">Wt:{{ $vehicle_detail->weight }}</td>
                                </tr>
                                @endforeach
                                </tr>
                            </tbody>
                        </table>

                        <center>BATTERIES DISCONNECTED &amp; GASOLINE DRAINED</center>
                    </td>
                    <td>{{ $vehicle_weight }} LBS</td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align:initial;padding-right:5px;">
                        DELIVERED BY:

                        <br>LIGHTER TRUCK-------------------------------------------------------------

                        <br>ARRIVED-DATE-------------------------------TIME-----------------------

                        <br>UNLOADED-DATE--------------------------TIME-----------------------

                        <br>CHECKED BY------------------------------------------------------------------

                        <br>PLACED &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span
                                style="font-size:9px;">IN SHIP</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        LOCATION-----------------
                        <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span
                                style="font-size:9px;">ON DOCK</span>
                    </td>
                    <td colspan="3" style="text-align:initial;padding-right:5px;">
                        <i>
                            RECEIVED THE ABOVE DESCRIBED GOODS OR PACKAGES SUBJECT TO
                            ALL THE TERMS OF THE UNDERSIGNED'S REGULAR FORM OF DOCK
                            RECEIPT AND BILL OF LADING WHICH SHALL CONSTITUTE THE
                            CONTRACT UNDER WHICH THE GOODS ARE RECEIVED, COPIES OF
                            WHICH ARE AVAIABLE FROM THE CARRIER ON REQUEST AND MAY BE
                            INSPECTED AT ANY OF ITS OFFICES.
                        </i>
                        <br>
                        <div style="padding:5px;">

                            <span style="">FOR THE MASTER</span><br>
                            <table style="border:none;">
                                <tbody>
                                <tr style="width: 50%;float: left;">
                                    <td style="border:none;padding: 10px;">BY</td>
                                    <td style="border:none;padding: 10px;"><span style="border-bottom-width: 1px;
                                                             border-bottom-style: solid;
                                                             border-bottom-color: #000;
                                                             margin-top: 10px;
                                                             margin-bottom: 10px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span>
                                    </td>
                                </tr>
                                <tr style="width: 50%;float: left;">
                                    <td style="border:none;padding: 10px;">
                                        DATE
                                    </td>
                                    <td style="border:none;padding: 10px;">
                    <span style="border-bottom-width: 1px;
                          border-bottom-style: solid;
                          border-bottom-color: #000;
                          margin-top: 10px;
                          margin-bottom: 10px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
