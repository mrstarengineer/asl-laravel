<style>
    body {
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
    }
    table.inner,
    table.inner th,
    table.inner td {
        border: 1px solid black;
         border-collapse: collapse;
    }

    table.inner th {
        padding-top: 5px;
        padding-bottom: 5px;
        font-size: 18px;
    }

    table.inner td {
        padding-left: 4px;
        padding-top: 2px;
        padding-bottom: 2px;
        font-size: 16px;
    }

    tr.car_list td {
        padding-top: 3px;
        padding-bottom: 3px;
        border: none;
        line-height: 11px;
    }

    tr.car_list_heading th {
        font-size: 12px;
    }

</style>

<div id="modal_content">
    <div class="cond_here" contenteditable="true">
        <div class="toppper">
            <table width="100%">
                <tbody>
                <tr>
                    <th width="15%" rowspan="2">
                        <img src="{{ asset('images/department-of-homeland-security-logo.jpg') }}" width="80" height="80">
                    </th>
                    <td width="85%" align="left">
                        <span style="font-size:18px;"><strong>U.S. CUSTOMS &amp; BORDER PROTECTION</strong></span><br>
                        <span style="font-size:18px;"><strong>VEHICLE EXPORT COVER SHEET</strong></span>
                    </td>
                </tr>
                <tr>
                    <td align="left" style="padding-left:4px; border:1px solid black;">PORT OF EXPORT : <span style="font-family:Arial, Helvetica, sans-serif;">HOUSTON APM BARBOURS CUT</span></td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="lefti pika" style="line-height:8px; margin-top: 10px;">
            <table width="100%" class="inner">
                <thead>
                <tr>
                    <th colspan="7" class="spec1"><strong>DESCRIPTION OF VEHICLE/EQUIPMENT</strong></th>
                </tr>
                <tr class="car_list_heading">
                    <th width="5%">YEAR</th>
                    <th width="15%">MAKE</th>
                    <th width="15%">MODEL</th>
                    <th width="25%">VIN</th>
                    <th width="25%">TITLE NUMBER</th>
                    <th width="20%">STATE</th>
                    <th width="10%">VALUE</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($export->vehicles as $vehicle)
                <tr class="car_list">
                    <td align="center">{{ $vehicle->year }}</td>
                    <td align="center" style="border-left: 1px solid black;">{{ $vehicle->make }}</td>
                    <td align="center" style="border-left: 1px solid black;">{{ $vehicle->model }}</td>
                    <td align="center" style="border-left: 1px solid black;">{{ $vehicle->vin }}</td>
                    <td align="center" style="border-left: 1px solid black;">{{ $vehicle->towing_request->title_number }}</td>
                    <td align="center" style="border-left: 1px solid black;">{{ $vehicle->towing_request->title_state }}</td>
                    <td align="center" style="border-left: 1px solid black;">${{ $vehicle->value }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="informations" style="line-height:12px; margin-top: 10px;">
            <table width="100%" class="inner">
                <tbody>
                <tr>
                    <th colspan="2">TRANSPORTATION INFORMATION</th>
                </tr>
                <tr>
                    <td width="50%">ITN : <span class="inputtext">{{ $export->itn }}</span></td>
                    <td width="50%">VALUE : <span class="inputtext">{{ data_get($export, 'houstan_custom_cover_letter.transportation_value') }}</span></td>
                </tr>
                <tr>
                    <td width="50%">CARRIER : <span class="inputtext">{{ $export->streamship_line }}</span></td>
                    <td width="50%">VESSEL : <span class="inputtext">{{ $export->vessel }}</span></td>
                </tr>
                <tr>
                    <td colspan="2" width="100%">BoL/AWB/BOOKING # : <span class="inputtext">{{ $export->booking_number }}</span></td>
                </tr>
                <tr>
                    <td width="50%">EXPORT DATE : <span class="inputtext">{{ $export->export_date }}</span></td>
                    <td width="50%">PORT OF UNLADING : </td>
                </tr>
                <tr>
                    <td colspan="2" width="100%">ULTIMATE DESTINATION : <span class="inputtext">{{ $export->destination }}</span></td>
                </tr>
                <tr>
                    <td colspan="2" width="100%">VEHICLE LOCATION : <span class="inputtext"> {{ data_get($export, 'houstan_custom_cover_letter.vehicle_location') }}</span></td>
                </tr>
                </tbody>
            </table>
            <br>

            <table width="100%" class="inner">
                <tbody>
                <tr>
                    <th colspan="4">SHIPPER/EXPORTER</th>
                </tr>
                <tr>
                    <td colspan="3" width="60%">NAME : <span class="inputtext"> {{ data_get($export, 'exporter.company_name', '') }}</span></td>
                    <td width="40%">DOB :  </td>
                </tr>
                <tr>
                    <td colspan="4" width="100%">ADDRESS:<span class="inputtext">{{ data_get($export, 'exporter.address_line_1', '') }}.</span>
                </td>
                </tr>
                <tr>
                    <td colspan="2" width="35%">
                        CITY : <span class="inputtext">{{ data_get($export, 'customer.city.name', '') }}</span>
                    </td>
                    <td width="30%">
                        STATE : <span class="inputtext">{{ data_get($export, 'customer.state.name', '') }}</span>
                    </td>
                    <td width="35%">
                        ZIP CODE : <span class="inputtext">{{ data_get($export, 'customer.zip_code', '') }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        PHONE : TEL:<span class="inputtext">{{ data_get($export, 'customer.phone', '') }}</span>,
                        FAX:<span class="inputtext">{{ data_get($export, 'customer.fax', '') }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" width="35%">
                        ID # : {{ data_get($export, 'houstan_custom_cover_letter.exporter_dob', '') }}</td>
                    <td colspan="2" width="65%">TYPE &amp; ISSUER : </td>
                </tr>
                </tbody>
            </table>
            <br>

            <table width="100%" class="inner">
                <tbody>
                <tr>
                    <th colspan="4">ULTIMATE CONSIGNEE<span style="font-weight:normal;"> ([&nbsp;&nbsp;&nbsp;&nbsp;] CHECK IF SHIPPER)</span></th>
                </tr>
                <?php
                if (data_get($export, 'houstan_custom_cover_letter.notify_party_item')) {
                ?>

                <tr>
                    <td colspan="3" width="60%">NAME : <span class="inputtext">{{ data_get($export, 'houstan_custom_cover_letter.notify_party_item.consignee_name') }}</span></td>
                    <td width="40%">DOB :</td>
                </tr>
                <tr>
                    <td colspan="4" width="100%">ADDRESS: <span class="inputtext"><?php echo $export->houstan_custom_cover_letter->notify_party_item->consignee_name . '&nbsp;' . $export->houstan_custom_cover_letter->notify_party_item->consignee_address_1; ?></span></td>
                </tr>
                <tr>
                    <td colspan="2" width="35%">CITY : <span class="inputtext"> <?php echo $export->houstan_custom_cover_letter->notify_party_item->city ?> </span></td>
                    <td width="30%">STATE : <span class="inputtext"> <?php echo $export->houstan_custom_cover_letter->notify_party_item->state ?> </span></td>
                    <td width="35%"><span class="inputtext">COUNTRY :  <?php echo $export->houstan_custom_cover_letter->notify_party_item->country ?> </span></td>
                </tr>
                <tr>
                    <td colspan="4">PHONE : <span class="inputtext"> <?php echo $export->houstan_custom_cover_letter->notify_party_item->phone; ?> </span></td>
                </tr>
                <?php } ?>

                </tbody>
            </table>
            <br>
            <table width="100%" class="inner">
                <tbody>
                <tr>
                    <th colspan="2">DESIGNATED AGENT/BROKER/FREIGHT FORWARDER</th>
                </tr>
                <tr>
                    <td colspan="2">
                        <span class="inputtext">NAME : {{ env('APP_NAME') }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><span
                                class="inputtext">ADDRESS : <?php echo \App\Enums\Agent::Address; ?></span>
                    </td>
                </tr>
                <tr>
                    <td><span class="inputtext">CITY : <?php echo \App\Enums\Agent::CITY; ?></span>
                    </td>
                    <td><span class="inputtext">STATE : <?php echo \App\Enums\Agent::STATE; ?></span>
                    </td>
                </tr>
                <tr>
                    <td><span class="inputtext">PHONE : <?php echo \App\Enums\Agent::PHONE; ?></span>
                    </td>
                    <td>
                        <span class="inputtext">CONTACT :<?php echo \App\Enums\Agent::CONTACT; ?></span>
                    </td>
                </tr>
                </tbody>
            </table>
            <br>
            <p>
            </p>
            <center><span
                        style="text-align: center; letter-spacing: 6px; font-weight: bold; font-family:  Arial, Helvetica, sans-serif;">UNITED STATES CUSTOMS AND BORDER PROTECTION</span>
            </center>
            <p></p>


        </div>

    </div>
</div>
