<style>
    .bola {
        /* width: 800px; */
        margin-right: auto;
        margin-left: auto;
        font-size: 13px;
        font-family: Arial, Helvetica, sans-serif;
    }
    .bola .carsi {
        width: 100%;
        float: left;
    }

    .bola .shipa {
        float: left;
        width: 50%;
        margin-top: 5px;
        border-top-width: 2px;
        border-bottom-width: 2px;
        border-top-style: solid;
        border-bottom-style: solid;
        border-top-color: #000;
        border-bottom-color: #000;
        height: 134px;
    }

    .bola .shipa221 {
        float: left;
        width: 50%;
        border-bottom-width: 2px;
        border-bottom-style: solid;
        border-bottom-color: #000;
        height: 102px;
    }

    .bola .shipa2212 {
        float: left;
        width: 50%;
        border-bottom-width: 2px;
        border-bottom-style: solid;
        border-bottom-color: #000;
        height: 50px;
    }

    .bola .shipa1 .kiki {
        width: 100%;
        border-bottom-width: 2px;
        border-bottom-style: solid;
        border-bottom-color: #000;
        float: left;
    }

    .bola .shipa1234 .kiki {
        width: 100%;
        border-bottom-width: 2px;
        border-bottom-style: solid;
        border-bottom-color: #fff;
        float: left;
    }

    .bola .shipa1 {
        float: left;
        width: 49%;
        margin-top: 5px;
        border-top-width: 2px;
        border-bottom-width: 2px;
        border-top-style: solid;
        border-bottom-style: solid;
        border-top-color: #000;
        border-bottom-color: #000;
        height: 134px;
        border-left-width: 2px;
        border-left-style: solid;
        border-left-color: #000;
    }

    .bola .shipa1234 {
        float: left;
        width: 49%;
        border-bottom-width: 2px;
        border-bottom-style: solid;
        border-bottom-color: #000;
        height: 102px;
        border-left-width: 2px;
        border-left-style: solid;
        border-left-color: #000;
    }

    .bola .shipa12342 {
        float: left;
        width: 49%;
        border-bottom-width: 2px;
        border-bottom-style: solid;
        border-bottom-color: #000;
        height: 50px;
        border-left-width: 2px;
        border-left-style: solid;
        border-left-color: #000;
    }

    .bola .shipa2212 .simi {
        height: 50px;
        width: 160px;
        float: left;
        border-right-width: 2px;
        border-right-style: solid;
        border-right-color: #000;
    }

    .bola .shipa2212 .simi1 {
        height: 50px;
        width: 106px;
        float: left;
    }

    .bola .desc {
        width: 100%;
        border-bottom-width: 2px;
        border-bottom-style: solid;
        border-bottom-color: #000;
        float: left;
    }

    .bola .shipa1 .kiki .pipi1 {
        width: 49%;
        border-right-width: 1px;
        border-right-style: solid;
        border-right-color: #000;
        float: left;
    }

    .bola .shipa1234 .kiki .pipi1 {
        width: 49%;
        border-right-width: 1px;
        border-right-style: solid;
        border-right-color: #000;
        float: left;
    }

    .bola .shipa1234 #KIAM {
        float: right;
    }

    .bola .shipa1234 #siam {
        font-size: 16px;
        float: right;
    }

    .bola .shipa1 .pipi12 {
        width: 100%;
        float: left;
    }

    .bola .shipa1234 .pipi12 {
        width: 100%;
        float: left;
    }

    .bola .shipa1 .kiki .pipi2 {
        width: 49%;
        float: left;
    }

    .bola .shipa1234 .kiki .pipi2 {
        width: 49%;
        float: left;
    }

    .bola .lefti tr #lopa {
        font-size: 21px;
    }

    th {
        text-align: left;
    }
    td {
        height: 22px !important;
    }
</style>
<div id="btnlanding" class="condition_reports">
    <div class="bola">
        <table class="" width="100%">
            <tbody>
            <tr>
                <td width="73%" id="lopa">
                    <strong>{{ empty( $export->broker_name ) ? 'Ariana Shipping Line LLC' : $export->broker_name }}</strong>
                    <p>{{ empty( $export->oti_number) ? 'OTI License #: 034147' : 'OTI License #: '.$export->oti_number }}</p>
                </td>
                <td width="27%"><strong>BILL OF LADING</strong></td>
            </tr>
            </tbody>
        </table>

        <div class="shipa">
            <table width="100%">
                <thead>
                <tr>
                    <td><b>SHIPPER / EXPORTER</b></td>
                </tr>
                </thead>

                <tbody>
                <tr>
                    <td contenteditable="true">  {{ data_get($export, 'exporter.company_name') . ' ' . data_get($export, 'exporter.address_line_1') }}</td>
                </tr>
                <tr>
                    <td contenteditable="true">{{ data_get($export, 'exporter.state.name') . ' ' . data_get($export, 'exporter.city.name') . ' ' . data_get($export, 'exporter.zip_code') }}</td>
                </tr>
                <tr>
                    <td contenteditable="true"> TEL: {{ data_get($export, 'exporter.phone') }}</td>
                </tr>
                </tbody>
            </table>


        </div>
        <div class="shipa1">
            <div class="kiki">
                <div class="pipi1">
                    <table width="100%">
                        <tbody>
                        <tr>
                            <td><b>BOOKING #</b></td>
                        </tr>
                        <tr>
                            <td contenteditable="true">{{ $export->booking_number }}</td>
                        </tr>
                        </tbody>
                    </table>

                </div>
                <div class="pipi2">
                    <table width="100%">
                        <tbody>
                        <tr>
                            <td><b>REFERENCE #</b></td>
                        </tr>
                        <tr>
                            <td contenteditable="true">{{ $export->ar_number }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="kiki">
                <div class="pipi1">
                    <table width="100%">
                        <tbody>
                        <tr>
                            <td><b>PLACE OF RECEIPT</b></td>
                        </tr>
                        <tr>
                            <td contenteditable="true">{{ trans('exports.port_of_loadings.'.$export->port_of_loading) }}</td>
                        </tr>
                        </tbody>
                    </table>

                </div>
                <div class="pipi2">
                    <table width="100%">
                        <tbody>
                        <tr>
                            <td><b>PORT OF LOADING</b></td>
                        </tr>
                        <tr>
                            <td contenteditable="true">{{ \Illuminate\Support\Arr::get(trans('exports.port_of_loadings'), $export->port_of_loading) }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pipi12">
                <table width="100%">
                    <tbody>
                    <tr>
                        <td width="50%"><b>PORT OF DISCHARGE:</b></td>
                        <td contenteditable="true">{{ \Illuminate\Support\Arr::get(trans('exports.port_of_discharges'), $export->port_of_discharge, '') }}</td>
                    </tr>
                    </tbody>
                </table>

            </div>

        </div>

        <div class="shipa221">
            <table width="100%">
                <thead>
                <tr>
                    <td><b>CONSIGNEE</b></td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td contenteditable="true">{{ data_get($export, 'houstan_custom_cover_letter.consignee_item.consignee_name') }}</td>
                </tr>
                <tr>
                    <td contenteditable="true">{{ data_get($export, 'houstan_custom_cover_letter.consignee_item.consignee_address_1') }}</td>
                </tr>
                <tr>
                    <td contenteditable="true">TEL: {{ data_get($export, 'houstan_custom_cover_letter.consignee_item.phone') }}</td>
                </tr>
                </tbody>

            </table>

        </div>
        <div class="shipa1234">
            <div class="kiki">
                <div class="pipi1">
                    <table width="100%">
                        <tbody>
                        <tr>
                            <td><b>PIER</b></td>
                        </tr>
                        <tr>
                            <td contenteditable="true">{{ $export->terminal }}</td>
                        </tr>
                        </tbody>
                    </table>

                </div>
                <div class="pipi2">
                    <table width="100%">
                        <tbody>
                        <tr>
                            <td><b>VESSEL</b></td>
                        </tr>
                        <tr>
                            <td contenteditable="true">{{ $export->vessel }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="kiki">
                <div class="pipi1">
                    <table width="100%">
                        <tbody>
                        <tr>
                            <td><b>LOADING PIER / TERMINAL</b></td>
                        </tr>
                        <tr>
                            <td contenteditable="true"><?= $export->terminal ?></td>
                        </tr>
                        </tbody>
                    </table>

                </div>
                <div class="pipi2">
                    <table width="100%">
                        <tbody>
                        <tr>
                            <td><b>VOYAGE NO.</b></td>
                        </tr>
                        <tr>
                            <td contenteditable="true">{{ $export->voyage }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div class="shipa221">
            <table width="100%">
                <thead>
                <tr>
                    <td><b>NOTIFY</b></td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td contenteditable="true">
                        <?php if ( data_get($export, 'houstan_custom_cover_letter.notify_party') ) { ?>
                        <?php
                        $id_consignee = data_get($export, 'houstan_custom_cover_letter.notify_party');
                        if ( $id_consignee ) {
                        $data_consignee = data_get($export, 'houstan_custom_cover_letter.notify_party_item');
                        }
                        }
                        if ($data_consignee){
                            echo $data_consignee->consignee_name;
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td contenteditable="true">{{ data_get($data_consignee, 'consignee_address_1') }}</td>
                </tr>
                <tr>
                    <td contenteditable="true"> TEL: {{ data_get($data_consignee, 'phone') }}</td>
                </tr>
                <tr></tr>
                </tbody>
            </table>
        </div>
        <div class="shipa1234">
            <table id="KIAM" width="100%">
                <tbody>
                <tr>
                    <td><b>FOR RELEASE OF CARGO PLEASE CONTACT:</b></td>
                </tr>
                <tr>
                    <td height="49"></td>
                </tr>
                </tbody>
            </table>
            <table id="siam" width="100%">
                <tbody>
                <tr>
                    <td width="12%"><b>ETA/</b></td>
                    <td width="88%" contenteditable="true">{{ $export->eta }}</td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="shipa2212">
            <div class="simi">
                <table width="100%">
                    <tbody>
                    <tr>
                        <td><b>CONTAINER #</b></td>
                    </tr>
                    <tr>
                        <td contenteditable="true">{{ $export->container_number }}</td>
                    </tr>
                    </tbody>
                </table>

            </div>
            <div class="simi">
                <table width="100%">
                    <tbody>
                    <tr>
                        <td><b>CONTAINER TYPE</b></td>
                    </tr>
                    <tr>
                        <td contenteditable="true" style="font-size:13px;">{{ trans('exports.container_types.'.$export->container_type) }}</td>
                    </tr>
                    </tbody>
                </table>

            </div>
            <div class="simi1">
                <table width="100%">
                    <tbody>
                    <tr>
                        <td><b>SEAL #</b></td>
                    </tr>
                    <tr>
                        <td contenteditable="true">{{ $export->seal_number }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="shipa12342">
            <table id="KIAM" width="100%">
                <tbody>
                <tr>
                    <td contenteditable="true"><b>SPECIAL INSTRUCTIONS:</b></td>
                </tr>
                <tr>
                    <td contenteditable="true">

                        <?= $export->special_instruction; ?>
                    </td>
                </tr>
                </tbody>
            </table>

        </div>

        @php $vehicle_weight = 0; $vehicle_weight_in_kg = 0;   @endphp

        <div class="desc">
            <table width="100%">
                <tbody>
                <tr>
                    <th width="60%" colspan="4"><b>SHIPPERS DESCRIPTIONS OF GOODS</b><br>{{ $export->all_vehicles->count() }} UNITS USED VEHICLE</th>
                    <th width="10%"><b>WEIGHT (KG)</b></th>
                    <th width="16%"><b>WEIGHT (LB)</b></th>
                    <th width="14%" contenteditable="true"><b>CUBE <br>55 M3</b></th>
                </tr>

                @foreach ($export->all_vehicles as $vehicle_detail)
                    @php
                        $vehicle_weight += (int) $vehicle_detail->weight;
                        $vehicle_weight_in_kg += (int) $vehicle_detail->weight_in_kg;
                    @endphp
                    <tr class="">
                        <td align="center" width="15%" contenteditable="true">{{ $vehicle_detail->year }}</td>
                        <td align="" width="15%" contenteditable="true">{{ $vehicle_detail->make }}</td>
                        <td align="" width="20%" contenteditable="true">{{ $vehicle_detail->model }} / {{ $vehicle_detail->color }}</td>
                        <td align="" width="30%" contenteditable="true">{{ $vehicle_detail->vin }}</td>
                        <td width="12%"
                            contenteditable="true">{{ round( (int) $vehicle_detail->weight_in_kg ) }}KG
                        </td>
                        <td width="12%"
                            contenteditable="true">{{ round( (int) $vehicle_detail->weight ) }}LB
                        </td>
                        <td width="16%"></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="carsi">
            <table class="" width="100%">
                <tbody>
                <tr>
                    <th width="26%"></th>
                    <th width="16%"></th>
                    <th width="16%"></th>
                    <th width="18%"></th>
                    <th width="10%" contenteditable="true">{{ round( $vehicle_weight_in_kg) }}KG</th>
                    <th width="10%" contenteditable="true"> <p style="margin-left: 20px">{{ round( $vehicle_weight  ) }}LB</p> </th>
                    <th width="4%"></th>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="addtls">
            <table width="100%">
                <tbody>
                <tr>
                    <td width="25%"><b>*** NON HAZ MAT</b></td>
                    <td width="35%"><b>OCEAN FREIGHT PRE-PAID</b></td>
                    <td width="20%"><b>TOTAL WEIGHT KG</b></td>
                    <td width="20%"><b>TOTAL WEIGHT LB</b></td>
                </tr>
                <tr>
                    <td><b>*** SEND TELEX RELEASE</b></td>
                    <td><b>ITN#</b><?= $export->itn ?></td>
                    <td>{{ round( $vehicle_weight_in_kg ) }}</td>
                    <td>{{ round( $vehicle_weight ) }}</td>
                </tr>
                <tr>
                    <td colspan="3">
                        <p>These Comodities, technology or software were exported from the United States in the
                            acordance with the export administrative regulations. Diversion contrary to the U.S. law
                            prohibited.</p>
                    </td>
                </tr>
                </tbody>
            </table>


        </div>

        <table class="bottom-text" width="100%">
            <tbody>
            <tr>
                <td>
                    HEREBY CERTIFY HAVING RECEIVED THE ABOVE DESCRIBED SHIPMENT IN OUTWARDLY GOOD CONDITION FROM THE
                    SHIPPER SHOWN IN SECTION "EXPORTER", FOR FORWARDING TO THE ULTIMATE CONSIGNEE SHOWN IN THE SECTION
                    "CONSIGNEE" ABOVE. IN WITNESS WHEREOF, THE ____________ NONNEGOTIABLE FCR'S HAVE BEEN SIGNED, AND IF
                    ONE (1) IS ACCOMPLISHED BY DELIVERY OF GOODS, ISSUANCE OF A DELIVERY ORDER OR BY SOME OTHER MEANS,
                    THE OTHERS SHALL BE AVOIDED IF REQUIRED BY THE FREIGHT FORWARDER, ONE (1) ORIGINAL FCR MUST BE
                    SURRENDERED, DULY ENDORSED IN EXCHANGE FOR THE GOODS OR DELIVERY ORDER.
                </td>
            </tr>
            </tbody>
        </table>

        <table class="" width="100%">
            <tbody>
            <tr>
                <td colspan=4>
                    <span style="display:block;margin-top:10px;">{{ env('APP_NAME') }} is a freight forwarding company, and we are not liable for any charges if your container is stopped by the US Customs for random, routine procedural checks.</span>
                    <span style="display:block;margin-top:10px;">
                        On our end, we will always make sure to have all the necessary paperwork attached when we ship your container and take the correct steps to meet all requirements.  However, due to US Customs policy, they can always stop a container for random inspections.  Although we will try our best to help you with anything we can, we are not responsible for this stop or any fees related to it because they are a completely separate entity from us.  You will be liable to US Customs and all charges pertaining to this stop will be covered by you and paid directly to them.
                    </span>
                </td>
            </tr>
            <tr>
                <td width="12%"><b>AUTHORIZED</b></td>
                <td width="42%" class="line_under" contenteditable="true">

                </td>
                <td width="11%"><b>DATED AT:</b></td>
                <td class="line_under" width="35%" contenteditable="true"></td>
            </tr>
            </tbody>
        </table>

    </div>
</div>
