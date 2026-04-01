<?php
use Illuminate\Support\Facades\DB;
?>
<style>
    #conditionreport {
        color: #000 !important;
    }
    #conditionreport table tbody tr td {
        text-align: initial;
    }
    .condition_reports td {
        font-size: 15px;
    }

    #btnlanding.condition_reports td {
        text-align: initial !important;
    }

    .white-box .tab-content {
        text-transform: uppercase;
    }
    .line_under {
        border-bottom-width: 1px;
        border-bottom-style: solid;
        border-bottom-color: #000;
    }
    .cond_here .lefti.title {
        font-size: 30px;
        font-weight: bold;
        margin-top: 20px;
        margin-left: 35px;
    }


    top_processes {
        width: 758px;
        float: left;
        padding: 5px;
    }

    .exports .lefti.pika table tbody {
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
    }

    .exports .top_processes.ui-state-highlight .filer #got {
        float: right;
    }

    #print {
        float: left;
        font-size: 14px;
    }

    .exports {
        margin-right: auto;
        margin-left: auto;
        font-size: 14px;
        /* width: 8in; */
        height: 10.5in;
        float: none;
    }

    table {
        margin-bottom: 2px;
    }

    .exports .toppper {
        float: left;
        width: 100%;
        text-align: center;
    }

    .spec {
        font-size: 14px;
        text-decoration: underline;
        padding-top: 4px;
        padding-bottom: 4px;
    }

    .spec1 {
        font-size: 14px;
    }

    .lefti {
        float: left;
    }

    .pisak {
        padding-bottom: 5px;
    }

    .pika {
        width: 100%;
    }

    .exports .informations {
        float: left;
        width: 100%;
        margin-top: 10px;
    }

    .line.under {
        border-bottom-width: 1px;
        border-bottom-style: solid;
        border-bottom-color: #000;
        margin-top: 10px;
        /*font-family: "Courier New", Courier, monospace;*/
        margin-bottom: 10px;
    }

    .kk {
        font-size: 12px;
    }

    .line.underp {
        border-bottom-width: 2px;
        border-bottom-style: solid;
        border-bottom-color: #000;
        margin-bottom: 10px;
    }

    .pio {
        font-size: 12px;
    }

    label {
        text-transform: UPPERCASE;
        font-size: 14px;
    }

    #customer_address {
        width: 93%;
        margin-left: 7px;
    }

    #modal-report .modal-content {
        color: #000;
    }

    .check-box-keys label {
        padding-left: 78px;
    }

    .condition_reports td {
        font-size: 15px;
    }

    #btnlanding.condition_reports td {
        text-align: initial !important;
    }

    .white-box .tab-content {
        text-transform: uppercase;
    }



    .condition_report .cond_here {
        float: left;
        /*width: 8in;*/
        margin-top: 3px;
    }

    .condition_report .cond_here .lefti {
        float: left;
    }

    .cond_here .lefti.title {
        font-size: 30px;
        font-weight: bold;
        margin-top: 20px;
        margin-left: 35px;
    }

    .condition_report .cond_here .basic_info {
        float: left;
        /*width: 8in;*/
        margin-top: 10px;
    }

    .condition_report .cond_here .basic_info .part1 {
        float: left;
        width: 55%;
    }

    .condition_report .cond_here .basic_info .part2 {
        float: left;
        width: 44%;
        margin-left: 1%;
    }

    .cond_here .basic_info .checklist {
        width: 99%;
        float: left;
        margin-top: 5px;
        border: 1px solid #000;
        margin-left: 3px;
    }

    .cond_here .basic_info .dimen {
        width: 99%;
        float: left;
        margin-left: 3px;
        border-right-width: 1px;
        border-bottom-width: 1px;
        border-left-width: 1px;
        border-right-style: solid;
        border-bottom-style: solid;
        border-left-style: solid;
        border-right-color: #000;
        border-bottom-color: #000;
        border-left-color: #000;
    }


    /*******/

    .cond_here .basic_info .sign {
        width: 99%;
        /* float: left; */
        margin-left: 3px;
        margin-top: 30px;
    }

    .cond_here .basic_info .sign table tr .leni {
        float: left;
        width: 350px;
        margin-right: 14px;
    }

    .cond_here .basic_info .sign table tr .leni1 {
        /* float: left; */
        width: 140px;
        margin-left: 15px;
    }

    .cond_here .basic_info .papugay {
        width: 99%;
        /* float: left; */
        margin-top: 5px;
        border: 1px solid #000;
        margin-left: 3px;
    }

    .cond_here .basic_info .condition {
        width: 99%;
        /* float: left; */
        margin-left: 3px;
        border-right-width: 1px;
        border-bottom-width: 1px;
        border-left-width: 1px;
        border-right-style: solid;
        border-bottom-style: solid;
        border-left-style: solid;
        border-right-color: #000;
        border-bottom-color: #000;
        border-left-color: #000;
        margin-bottom: 10px;
    }

    .cond_here .basic_info .picas1 {
        width: 100%;
        height: 140px;
        margin-bottom: 10px;
        border: 1px solid #000;
    }

    .cond_here .basic_info .picas2 {
        width: 100%;
        height: 140px;
        margin-bottom: 10px;
        border: 1px solid #000;
    }

    .cond_here .basic_info .picas3 img {
        margin-left: 2px;
        margin-top: 2px;
        margin-bottom: 2px;
    }

    .cond_here .basic_info .picas3 {
        border: 1px solid #000;
    }

    .cond_here .basic_info .picas4 {
        /* float: left; */
        border-right-width: 1px;
        border-bottom-width: 1px;
        border-right-style: solid;
        border-bottom-style: solid;
        border-right-color: #000;
        border-bottom-color: #000;
        width: 375px;
    }

    .cond_here .basic_info .picas4 #yoba {
        float: left;
        width: 100%;
        border-top-width: 1px;
        border-top-style: solid;
        border-top-color: #000;
    }

    .cond_here .basic_info .picas3 #yoba {
        border-top-width: 1px;
        border-top-style: solid;
        border-top-color: #000;
        /* float: left; */
        width: 100%;
    }

    .cond_here .basic_info #yoba table tr .line_right {
        border-right-width: 1px;
        border-right-style: solid;
        border-right-color: #000;
    }

    .condition_report .cond_here .basic_info .picas4 .lefti {
        float: left;
    }

    .cond_here .basic_info table thead tr .biga {
        font-size: 14px;
    }

    .cond_here .basic_info table thead tr .biga1 {
        font-size: 12px;
    }

    .picas1 .lefti,
    .picas2 .lefti {
        width: 39%;
    }

    .cond_here .basic_info .picas2 #piss2 {
        width: 60%;
        border-left-width: 1px;
        border-left-style: solid;
        border-left-color: #000;
        height: 125px;
        float: left;
    }

    .borders-here {
        border-left-width: 1px;
        border-left-style: solid;
        border-left-color: #000;
        border-right-width: 1px;
        border-right-style: solid;
        border-right-color: #000;
    }

    .customer_part {
        margin-top: 15px;
        margin-bottom: 15px;
    }

</style>
<!-- Latest compiled and minified CSS -->
<div id="conditionreport" class="condition_reports">
    <div class="cond_here">
        <div class="row">
            <div class="col-md-12">
                <div class="cond_logo lefti">
                    <img src="{{ url('images/logo.jpg') }}" height="72" class="print-logo" alt="ArianaW Logo">
                </div>

                <div class="lefti title">Vehicle Condition Report</div>
            </div>
        </div>
        <div class="basic_info">
            <div style="margin-bottom: 10px;" class="row">
                <div class="col-md-6">
                    <div class="part1">
                        <table width="100%">
                            <tbody>
                            <tr>
                                <td width="18%"><b>Customer</b></td>
                                <td width="82%" class="line_under"
                                    id="CUSTOMER NAME"><?= $model->customer->company_name; ?></td>
                            </tr>
                            </tbody>
                        </table>

                        <table width="100%">
                            <tbody>
                            <tr>
                                <td width="18%"><b>Address</b></td>
                                <td width="72%" class="line_under"
                                    id="Address L1">{{ data_get($model, 'location.name') }}</td>

                            </tr>
                            </tbody>
                        </table>

                        <table width="100%">
                            <tbody>
                            <tr>
                                <td width="18%"><b>Phone #</b></td>
                                <td width="44%" class="line_under"
                                    id="Phone Number"><?= $model->customer->phone; ?></td>
                                <td width="14%"><b>Weight</b></td>
                                <td width="24%" class="line_under" id="Weight"><?= $model->weight; ?></td>
                            </tr>
                            </tbody>
                        </table>

                        <table width="100%">
                            <tbody>
                            <tr>
                                <td width="18%"><b>Lot #</b></td>
                                <td width="44%" class="line_under" id="Lot Number"><?= $model->lot_number; ?></td>
                                <td width="14%"><b>Inv #</b></td>
                                <td width="24%" class="line_under" id="Hat Number"><?= $model->hat_number; ?></td>
                            </tr>
                            </tbody>
                        </table>

                        <table width="100%">
                            <tbody>
                            <tr>
                                <td width="18%"><b>Destination</b></td>
                                <td width="82%" class="line_under" id="Destination"></td>
                            </tr>
                            </tbody>
                        </table>

                        <table width="100%">
                            <tbody>
                            <tr>
                                <td width="6%"><b>Condition</b></td>
                                <td width="24%" class="line_under"
                                    id="Condition"><?php if ( optional( $model->towing_request )->condition == '1' ) {
                                        echo 'Operable';
                                    } else {
                                        echo 'Non Operable';
                                    } ?></td>
                                <td width="6%"><b>Damaged</b></td>
                                <td width="24%" class="line_under"
                                    id="Damages"><?php if ( $model->towing_request->damaged == '1' ) {
                                        echo 'Yes';
                                    } else {
                                        echo 'No';
                                    }; ?></td>
                            </tr>
                            </tbody>
                        </table>


                    </div>
                </div>

                <div class="col-md-6">
                    <div class="part2">
                        <table width="100%">
                            <tbody>
                            <tr>
                                <td width="15%"><b>Year</b></td>
                                <td width="20%" class="line_under" id="Year"><?= $model->year; ?></td>
                                <td width="11%"><b>Color</b></td>
                                <td width="20%" class="line_under" id="Color"><?= $model->color; ?></td>
                                <td width="12%"><b>VCR #</b></td>
                                <td width="30%" class="line_under" id="Color"><?= $model->vcr; ?></td>
                            </tr>
                            </tbody>
                        </table>
                        <table width="100%">
                            <tbody>
                            <tr>
                                <td width="18%"><b>Model</b></td>
                                <td width="38%" class="line_under" id="Model"><?= $model->model; ?></td>
                                <td width="11%"><b>Make</b></td>
                                <td width="33%" class="line_under" id="Make"><?= $model->make; ?></td>
                            </tr>
                            </tbody>
                        </table>

                        <table width="100%">
                            <tbody>
                            <tr>
                                <td width="18%"><b>VIN</b></td>
                                <td width="82%" class="line_under" id="VIN"><?= $model->vin; ?></td>
                            </tr>
                            </tbody>
                        </table>

                        <table width="100%">
                            <tbody>
                            <tr>
                                <td width="18%"><b>License#</b></td>
                                <td width="82%" class="line_under"
                                    id="License Number"><?= $model->license_number; ?></td>
                            </tr>
                            </tbody>
                        </table>

                        <table width="100%">
                            <tbody>
                            <tr>
                                <td width="30%"><b>Towed From</b></td>
                                <td width="70%" class="line_under" id="Towed From"><?= $model->towed_from; ?></td>
                            </tr>
                            </tbody>
                        </table>

                        <table width="100%">
                            <tbody>
                            <tr>
                                <td width="30%"><b>Tow Amount</b></td>
                                <td width="31%" class="line_under" id="Tow Amount"><?= $model->towed_amount; ?></td>
                                <td width="20%"><b>Storage Amount</b></td>
                                <td width="14%" class="line_under" id="Storage"><?= $model->storage_amount; ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">

                    <table width="100%">
                        <tbody>
                        <tr>
                            <td width="6%"><b>Towed </b></td>
                            <td width="12%" class="line_under"
                                id="Damages"><?php if ( $model->towing_request->towed == '1' ) {
                                    echo 'Yes';
                                } else {
                                    echo 'No';
                                }; ?></td>
                            <td width="21%"><b>Title Provided</b></td>
                            <td width="11%" class="line_under"
                                id="Damages"><?php if ( $model->towing_request->title_received == '1' ) {
                                    echo 'Yes';
                                } else {
                                    echo 'No';
                                }; ?></td>
                            <td width="6%"><b>Pictures</b></td>
                            <td width="24%" class="line_under"
                                id="Damages"><?php if ( $model->towing_request->pictures == '1' ) {
                                    echo 'Yes';
                                } else {
                                    echo 'No';
                                }; ?></td>

                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="checklist">
                <table width="100%">
                    <thead>
                    <tr>
                        <th class="biga">CHECK OPTIONS INCLUDED IN VEHICLE</th>
                    </tr>
                    </thead>
                </table>

                <table width="100%">
                    <tbody>

                    <tr>
                        <?php
                        $i = 0;
                        foreach($features as $feature) {
                        $featuredata = \App\Models\VehicleFeature::where( [ 'vehicle_id' => $model->id, 'features_id' => $feature->id, 'value' => 1 ] )->first();
                        $checked = false;
                        if ( $featuredata ) {
                            $checked = true;
                        }
                        if ( $model->keys == '1' && $feature[ 'id' ] == '1' ) {
                            $checked = true;
                        }
                        if($i !== 6 )
                        {?>
                        <?php
                        if($checked == true){?>
                        <td><input disabled="true" name="Keys" <?php  if ( $checked == true ) {
                                echo "checked='checked'";
                            } ?> type="checkbox"><?php echo $feature[ 'name' ]; ?></td>
                        <?php }else{?>
                        <td><input disabled="true" name="Keys" <?php  if ( $checked == true ) {
                                echo "checked='checked'";
                            } ?> type="checkbox"><?php echo $feature[ 'name' ]; ?></td>

                        <?php } ?>
                        <?php
                        }else{
                        ?>
                    </tr>
                    <tr>
                        <?php    if($checked == true){?>

                        <td><input disabled="true" name="Keys" <?php  if ( $checked == true ) {
                                echo "checked='checked'";
                            } ?> type="checkbox"><?php echo $feature[ 'name' ]; ?></td>
                        <?php }else{?>
                        <td><input disabled="true" name="Keys" <?php  if ( $checked == true ) {
                                echo "checked='checked'";
                            } ?> type="checkbox"><?php echo $feature[ 'name' ]; ?></td>
                        <?php } ?>
                        <?php
                        }
                        $i++;
                        }
                        ?>
                    </tr>


                    </tbody>
                </table>

            </div>
            <div class="condition">
                <table width="100%">
                    <thead>
                    <tr>
                        <th class="biga">CONDITION OF VEHICLE</th>
                    </tr>
                    <tr>
                        <th class="biga1">Indicate any damage to the vehicle in the space provided using your own words
                            or the following legend. If None write None
                        </th>
                    </tr>
                    </thead>
                </table>

                <table id="Sik" width="100%">
                    <tbody>
                    <tr>
                        <td>H - Hairline Scratch</td>
                        <td>PT - Pitted</td>
                        <td>T - Torn</td>
                        <td>B - Bent</td>
                        <td>GC - Glass Cracked</td>
                        <td>M - Missing</td>
                    </tr>
                    <tr>
                        <td>SM - Smashed</td>
                        <td>R - Rusty</td>
                        <td>CR - Creased</td>
                        <td>S - Scratched</td>
                        <td>ST - Stained</td>
                        <td>BR - Broken</td>
                        <td>D - Dented</td>
                    </tr>
                    </tbody>
                </table>


            </div>
            <?php
            $features = DB::select( DB::raw( 'SELECT * FROM `conditions` f left join vehicle_conditions vf on vf.condition_id=f.id where  vf.vehicle_id =' . $model->id . '  ;' ) );
            if($features){
            ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="picas1">
                        <span class="lefti"><img width="155" height="120" src="{{ url('images/Car-Front.jpg') }}"></span>
                        <span class="lefti" id="piss">
                    <?php if(isset( $features[ 0 ]->value )){ ?>
                    <div class="line_under"> <table><tbody><tr><td>1</td><td align="center"
                                                                             id="1"><?php echo $features[ 0 ]->value; ?></td></tr></tbody></table></div>
                    <?php } if(isset( $features[ 1 ]->value )){ ?>
                    <div class="line_under"> <table><tbody><tr><td>2</td><td align="center"
                                                                             id="2"><?php echo $features[ 1 ]->value; ?></td></tr></tbody></table></div>
                    <?php } if(isset( $features[ 2 ]->value )){ ?>
                    <div class="line_under"> <table><tbody><tr><td>3</td><td align="center"
                                                                             id="3"><?php echo $features[ 2 ]->value; ?></td></tr></tbody></table></div>
                    <?php } if(isset( $features[ 3 ]->value )){ ?>
                    <div class="line_under"> <table><tbody><tr><td>4</td><td align="center"
                                                                             id="4"><?php echo $features[ 3 ]->value; ?></td></tr></tbody></table></div>
                    <?php } if(isset( $features[ 4 ]->value )){ ?>
                    <div class="line_under"> <table><tbody><tr><td>5</td><td align="center"
                                                                             id="5"><?php echo $features[ 4 ]->value; ?></td></tr></tbody></table></div>
                    <?php } ?>
                </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="picas2">
                        <span class="lefti"><img width="179" height="120" src="{{ url('images/Car-Back.jpg') }}"></span>
                        <span class="lefti" id="piss2">
                <?php if(isset( $features[ 5 ]->value )){ ?>
                    <div class="line_under"><table><tbody><tr>
                                    <td>6</td><td align="center" id="6"><?php echo $features[ 5 ]->value; ?></td></tr></tbody></table></div>
                    <?php } if(isset( $features[ 6 ]->value )){ ?>
                    <div class="line_under"> <table><tbody><tr>
                                    <td>7</td><td align="center"
                                                  id="7"><?php echo $features[ 6 ]->value; ?></td></tr></tbody></table></div>
                    <?php } if(isset( $features[ 7 ]->value )){ ?>
                    <div class="line_under"> <table><tbody><tr>
                                    <td>8</td><td align="center"
                                                  id="8"><?php echo $features[ 7 ]->value; ?></td></tr></tbody></table></div>
                    <?php } if(isset( $features[ 8 ]->value )){ ?>
                    <div class="line_under"> <table><tbody><tr>
                                    <td>9</td><td align="center"
                                                  id="9"><?php echo $features[ 8 ]->value; ?></td></tr></tbody></table></div>
                    <?php } if(isset( $features[ 9 ]->value )){ ?>
                    <div class="line_under"> <table><tbody><tr><td>10</td><td align="center"
                                                                              id="10"><?php echo $features[ 9 ]->value; ?></td></tr></tbody></table></div>
                    <?php } ?>
                </span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="picas3">
                        <div class="">
                            <img src="{{ url('images/driver.jpg') }}" width="384" height="141"></div>
                        <div id="yoba">
                            <table width="100%">
                                <tbody>
                                <tr>
                                    <?php if(isset( $features[ 10 ]->value )){ ?>
                                    <td width="6%">11</td>
                                    <td class="line_right" align="center"
                                        width="28%"><?php echo $features[ 10 ]->value; ?></td>
                                    <?php } if(isset( $features[ 11 ]->value )){ ?>
                                    <td width="6%">12</td>
                                    <td class="line_right" align="center"
                                        width="27%"><?php echo $features[ 11 ]->value; ?></td>
                                    <?php } if(isset( $features[ 12 ]->value )){ ?>
                                    <td width="6%">13</td>
                                    <td align="center" width="27%"><?php echo $features[ 12 ]->value; ?></td>
                                    <?php } ?>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div id="yoba">
                            <table width="100%">
                                <tbody>
                                <tr>
                                    <?php if(isset( $features[ 13 ]->value )){ ?>
                                    <td width="6%">14</td>
                                    <td align="center" class="line_right"
                                        width="28%"><?php echo $features[ 13 ]->value; ?></td>
                                    <?php } if(isset( $features[ 14 ]->value )){ ?>
                                    <td width="6%">15</td>
                                    <td align="center" class="line_right"
                                        width="27%"><?php echo $features[ 14 ]->value; ?></td>
                                    <?php } if(isset( $features[ 15 ]->value )){ ?>
                                    <td width="6%">16</td>
                                    <td align="center" width="27%"><?php echo $features[ 15 ]->value; ?></td>
                                    <?php }?>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <div class="col-md-6">

                    <div class="picas3">
                        <div class="">
                            <img src="{{ url('images/Passenger.jpg') }}" width="384" height="141"></div>
                        <div id="yoba">
                            <table width="100%">
                                <tbody>
                                <tr>
                                    <?php if(isset( $features[ 16 ]->value )){ ?>
                                    <td width="6%">17</td>
                                    <td align="center" class="line_right"
                                        width="28%"><?php echo $features[ 16 ]->value; ?></td>
                                    <?php } if(isset( $features[ 17 ]->value )){ ?>
                                    <td width="6%">18</td>
                                    <td align="center" class="line_right"
                                        width="27%"><?php echo $features[ 17 ]->value; ?></td>
                                    <?php } if(isset( $features[ 18 ]->value )){ ?>
                                    <td width="6%">19</td>
                                    <td align="center" width="27%"><?php echo $features[ 18 ]->value; ?></td>
                                    <?php } ?>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div id="yoba">
                            <table width="100%">
                                <tbody>
                                <tr>
                                    <?php if(isset( $features[ 19 ]->value )){ ?>
                                    <td width="6%">20</td>
                                    <td align="center" class="line_right"
                                        width="28%"><?php echo $features[ 19 ]->value; ?></td>
                                    <?php } if(isset( $features[ 20 ]->value )){ ?>
                                    <td width="6%">21</td>
                                    <td align="center" class="line_right"
                                        width="27%"><?php echo $features[ 20 ]->value; ?></td>
                                    <?php } if(isset( $features[ 21 ]->value )){ ?>
                                    <td width="6%">&nbsp;</td>
                                    <td width="27%"></td>
                                    <?php } ?>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>


                </div>
            </div>
            <?php } ?>
            <div class="papugay">
                <table width="100%">
                    <tbody>
                    <tr>
                        <td><b>1.</b> Liability-Shipper (customer) must have door-to-door insurance while goods are in
                            warehouse and during transit. {{ env('APP_NAME') }} will not
                            assume any responsibility for uninsured or underinsured shipment(s).
                        </td>
                    </tr>

                    <tr>
                        <td><b>2.</b> Rates for individual cars are based on consolidation; company is not responsible
                            for exact shipping dates. Company is not responsible for delays
                            in shipping schedules and/or transit time or custom charges and delays..
                        </td>
                    </tr>
                    </tbody>
                </table>

            </div>

            <div class="dimen">
                <table width="100%">
                    <tbody>
                    <tr>
                        <td>
                            <b>DIMENSIONS: </b>
                            The above is an accurate representation of the condition of the vehicle at the time of
                            loading. NOTICE: The OWNER'S or AUTHORIZED AGENT'S
                            Signature of the origin is also to the following RELEASE: this will authorize CARRIER to
                            drive my vehicle either at origin destination between point
                            (s) of loading/unloading and the point(s) of pick-up/delivery.
                        </td>
                    </tr>
                    </tbody>
                </table>

            </div>

            <div class="sign">
                <table width="100%">
                    <tbody>
                    <tr>
                        <td>
                            This above Vehicle has been delivered in the condition described.
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="sign">
                <table>
                    <tbody>
                    <tr>
                        <td class="leni line_under">&nbsp;
                            <?php //echo 'JMKC EXPRESS';?>
                        </td>
                        <td class="leni1 line_under">
                            <?php //$model->towing_request->deliver_date;?>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" class="leni ">
                            <b>Completed By</b>
                        </td>
                        <td align="center" class="leni1 ">
                            <b>Date</b>
                        </td>
                    </tr>


                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="customer_part">
        <div class="pics">
            <?php
                $images = $model->vehicle_image;
                foreach ($images as $image) {
            ?>
            <div style="margin-bottom: 10px; margin-right: 10px; float: left;">
                <img height="230px" width="275px" src="{{ Storage::url( str_replace( env('AWS_S3_BASE_URL'), '', $image->name )) }}">
            </div>
            <?php } ?>
        </div>
    </div>

</div>