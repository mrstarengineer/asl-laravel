<style>
    top_processes {
        width: 758px;
        float: left;
        padding: 5px;
    }

    .exports .lefti.pika table tbody {
        font-family: "Courier New", Courier, monospace;
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
        font-family: Arial, Helvetica, sans-serif;
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
        font-family: "Courier New", Courier, monospace;
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
</style>

<div id="modal_content">

    <div class="exports">
        <div class="toppper">
            <table width="100%">
                <tbody>
                <tr>
                    <th width="15%">
                        <img class="homeland-logo" src="{{ asset('images/department-of-homeland-security-logo.jpg') }}" width="80" height="80">
                    </th>
                    <td width="69%" align="center">
                        <b style="text-align:center;display:block;">U.S. CUSTOMS AND BORDER PROTECTION</b>
                        <div id="textArea" class="textAreaWrapper" style="height: 90px;text-align:center;">
                            <p style=" bottom:0; left:0;" contentEditable="true">
                                <?= isset( $expoter_info->address_line_2 ) ? $expoter_info->address_line_2 : "" ?>
                            </p>
                        </div>
                    </td>
                    <td width="16%"></td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="lefti pika">
            <table width="100%">
                <tbody>
                <tr>
                    <th border="1" class="spec line underp">COVER LETTER</th>
                </tr>
                </tbody>
            </table>
            <table width="100%">
                <tbody>
                <tr>
                    <th class="spec1" style="text-align:center;">VEHICLE INFORMATION</th>
                </tr>
                </tbody>
            </table>

            <table width="100%">
                <thead>
                <tr>
                    <th>YEAR</th>
                    <th>MAKE</th>
                    <th>MODEL</th>
                    <th>VIN</th>
                    <th>TITLE NUMBER</th>
                    <th>STATE</th>
                    <th>VALUE</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($export->vehicles as $vehicle_detail) {
                ?>
                <tr class="car_list">
                    <td align="center"><?php echo $vehicle_detail->year; ?></td>
                    <td align="center" style=""><?php echo $vehicle_detail->make; ?></td>
                    <td align="center" style=""><?php echo $vehicle_detail->model; ?></td>
                    <td align="center" style=""><?php echo $vehicle_detail->vin; ?></td>
                    <td align="center" style=""><?php echo $vehicle_detail->towing_request->title_number; ?></td>
                    <td align="center" style=""><?php echo $vehicle_detail->towing_request->title_state; ?></td>
                    <td align="center" style="">&#36;<?php echo $vehicle_detail->value; ?></td>
                    <td><button class="no-print" onclick="this.closest('tr').remove()">X</button></td>
                </tr>
                <?php }?>
                </tbody>
            </table>

        </div>
        <div class="informations">


            <table class="pisak line underp" width="100%">
                <tbody>
                <tr>


                    <td width="10%"><b>ROLLOVER</b></td>
                    <td width="6%" class="">____</td>
                    <td width="84%">(Please check if a cover letter was previously validated)</td>
                </tr>
                </tbody>
            </table>

            <table class="spec1" width="100%">
                <tbody>
                <tr>
                    <th style="text-align:center;">EXPORTER INFORMATION</th>
                </tr>
                </tbody>
            </table>

            <table width="100%">
                <tbody>
                <tr>
                    <td width="18%"><b>Exporter (USPPI) <br/>Name:</b></td>
                    <td width="75%" class="line under">
                        <div id="textArea" class="textAreaWrapper" style="">
                            <p style=" bottom:0; left:0;" contentEditable="true">
                                <?php if ( isset( $export->exporter->company_name ) ) {
                                    echo $export->exporter->company_name;
                                }?>
                            </p>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>

            <table width="100%">
                <tbody>
                <tr>
                    <td width="18%"><b>U.S. Address:</b></td>
                    <td width="32%" class="line under">
                        <div id="textArea" class="textAreaWrapper" style="">
                            <p style=" bottom:0; left:0;" contentEditable="true">
                                <?php echo isset( $export->customer->address_line_1 ) ? $export->customer->address_line_1 : ""; ?>
                            </p>
                        </div>
                    </td>
                    <td width="23%" class="line under">
                        <div id="textArea" class="textAreaWrapper" style="">
                            <p style=" bottom:0; left:0;" contentEditable="true">
                                <?php echo isset( $customer_detail->city ) ? $export->customer->city->name : ""; ?>
                            </p>
                        </div>
                    </td>
                    <td width="8%" class="line under">
                        <div id="textArea" class="textAreaWrapper" style="">
                            <p style=" bottom:0; left:0;" contentEditable="true">
                                <?php echo isset( $export->customer->state ) ? $export->customer->state->name : ""; ?>
                            </p>
                        </div>
                    </td>
                    <td width="15%" class="line under">
                        <div id="textArea" class="textAreaWrapper" style="">
                            <p style=" bottom:0; left:0;" contentEditable="true">
                                <?php echo isset( $export->customer->zip_code ) ? $export->customer->zip_code : ""; ?>
                            </p>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td width="18%"></td>
                    <td width="32%" align="center" class="pio">Street</td>
                    <td width="23%" align="center" class="pio">City</td>
                    <td width="13%" align="center" class="pio">State</td>
                    <td width="12%" align="center" class="pio">Zip</td>
                </tr>
                </tbody>
            </table>

            <table width="100%">
                <tbody>
                <tr>
                    <td width="18%"><b>Phone:</b></td>
                    <td width="33%" class="line under">
                        <div id="textArea" class="textAreaWrapper" style="">
                            <p style=" bottom:0; left:0;" contentEditable="true">
                                <?php echo isset( $export->customer->phone ) ? $export->customer->phone : ""; ?>
                            </p>
                        </div>
                    </td>
                    <td width="8%"><b>Fax:</b></td>
                    <td width="40%" class="line under">
                        <div id="textArea" class="textAreaWrapper" style="">
                            <p style=" bottom:0; left:0;" contentEditable="true">
                                <?php echo isset( $expoter_info->fax ) ? $expoter_info->fax : ""; ?>
                            </p>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>

            <table width="100%">
                <tbody>
                <tr>
                    <td width="18%"><b>Filing <br/>Agent/Freight<br/> Forwarder:</b></td>
                    <td width="34%" class="line under">
                        <div id="textArea" class="textAreaWrapper" style="height: 60px;">
                            <p style=" bottom:0; left:0;" contentEditable="true">
                                {{ env('APP_NAME') }}
                            </p>
                        </div>
                    </td>
                    <td width="10%"><b>Contact:</b></td>
                    <td width="40%" class="line under">
                        <div id="textArea" class="textAreaWrapper" style="">
                            <p style=" bottom:0; left:0;" contentEditable="true">
                                <?php echo isset( $expoter_info->customer_name ) ? $expoter_info->customer_name : ''; ?>                          </p>
                        </div>
                    </td>

                </tr>
                <!-- Nereida request Oct 6 2017-->
                <tr>
                    <td width="18%"><b>Loading <br/>location(if <br/>different from<br/> forwarder):</b></td>
                    <td width="34%" class="line under">
                        <div id="textArea" class="textAreaWrapper" style="height: 60px;">
                            <p style=" bottom:0; left:0;" contentEditable="true">
                                {{ env('APP_NAME') }}
                            </p>
                        </div>
                    </td>

                    <td width="10%"><b>Contact:</b></td>
                    <td width="40%" class="line under">
                        <div id="textArea" class="textAreaWrapper" style="">
                            <p style=" bottom:0; left:0;" contentEditable="true">
                                <?php echo isset( $expoter_info->customer_name ) ? $expoter_info->customer_name : ''; ?>
                            </p>
                        </div>
                    </td>

                </tr>
                </tbody>
            </table>

            <table width="100%">
                <tbody>
                <tr>
                    <td width="18%"><b>U.S. Address:</b></td>
                    <td width="32%" class="line under">
                        <div id="textArea" class="textAreaWrapper" style="">
                            <p style=" bottom:0; left:0;" contentEditable="true">
                                <?php echo isset( $expoter_info->address_line_1 ) ? $expoter_info->address_line_1 : '' ?>
                            </p>
                        </div>
                    </td>
                    <td width="23%" class="line under">
                        <div id="textArea" class="textAreaWrapper" style="">
                            <p style=" bottom:0; left:0;" contentEditable="true">
                                <?php echo isset( $expoter_info->city ) ? $expoter_info->city : '' ?>
                            </p>
                        </div>
                    </td>
                    <td width="8%" class="line under">
                        <div id="textArea" class="textAreaWrapper" style="">
                            <p style=" bottom:0; left:0;" contentEditable="true">
                                <?php echo isset( $expoter_info->state ) ? $expoter_info->state : ''?>
                            </p>
                        </div>
                    </td>
                    <td width="15%" class="line under">
                        <div id="textArea" class="textAreaWrapper" style="">
                            <p style=" bottom:0; left:0;" contentEditable="true">
                                <?php echo isset( $expoter_info->zip_code ) ? $expoter_info->zip_code : '' ?>
                            </p>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td width="18%"></td>
                    <td width="32%" align="center" class="pio">Street</td>
                    <td width="23%" align="center" class="pio">City</td>
                    <td width="12%" align="center" class="pio">State</td>
                    <td width="12%" align="center" class="pio">Zip</td>
                </tr>
                </tbody>
            </table>

            <table width="100%" class="line underp pisak">
                <tbody>
                <tr>
                    <td width="18%"><b>Phone:</b></td>
                    <td width="35%" class="line under">
                        <div id="textArea" class="textAreaWrapper" style="">
                            <p style=" bottom:0; left:0;" contentEditable="true">
                                <?php echo isset( $expoter_info->phone ) ? $expoter_info->phone : ''; ?>
                            </p>
                        </div>
                    </td>
                    <td width="8%"><b>Fax:</b></td>
                    <td width="42%" class="line under">
                        <div id="textArea" class="textAreaWrapper" style="">
                            <p style=" bottom:0; left:0;" contentEditable="true">
                                <?php echo isset( $expoter_info->fax ) ? $expoter_info->fax : ''; ?>
                            </p>
                        </div>
                    </td>

                </tr>
                </tbody>
            </table>

            <table class="spec1" width="100%">
                <tbody>
                <tr>
                    <th style="text-align:center;">EXPORT INFORMATION</th>
                </tr>
                </tbody>
            </table>

            <table width="100%">
                <tbody>
                <tr>
                    <td width="16%"><b>Booking #:</b></td>
                    <td width="22%" class="line under">
                        <div id="textArea" class="textAreaWrapper" style="">
                            <p style=" bottom:0; left:0;" contentEditable="true"><?= $export->booking_number;?></p>
                        </div>

                    </td>
                    <td width="25%"><b>Vessel Name &amp; Voyage#:</b></td>
                    <td width="40%" class="line under">
                        <div id="textArea" class="textAreaWrapper" style="">
                            <p style=" bottom:0; left:0;"
                               contentEditable="true"><?=$export->vessel . "<span style='margin-left:15px;'>" . $export->voyage . "</span>";?></p>
                        </div>
                    </td>

                </tr>
                </tbody>
            </table>

            <table width="100%">
                <tbody>
                <tr>
                    <td width="25%"><b>Vessel Departure Date:</b></td>
                    <td width="18%" class="line under">
                        <div id="textArea" class="textAreaWrapper" style="">
                            <p style=" bottom:0; left:0;" contentEditable="true">
                                <?=$export->export_date;?>
                            </p>
                        </div>
                    </td>
                    <td width="18%"><b>US Port of Export:</b></td>
                    <td width="40%" class="line under">
                        <div id="textArea" class="textAreaWrapper" style="">
                            <p style=" bottom:0; left:0;" contentEditable="true">
                                <?=$export->port_of_loading;?>
                            </p>
                        </div>
                    </td>

                </tr>
                </tbody>
            </table>

            <table width="100%">
                <tbody>
                <tr>
                    <td width="43%"><b>City and Country of Ultimate Destination:</b></td>
                    <td width="57%" class="line under">
                        <div id="textArea" class="textAreaWrapper" style="">
                            <p style=" bottom:0; left:0;" contentEditable="true">
                                <?=$export->destination;?>
                            </p>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>

            <table width="100%" class="line underp pisak">
                <tbody>
                <tr>
                    <td width="18%"><b>Steamship Line:</b></td>
                    <td width="20%" class="line under">
                        <div id="textArea" class="textAreaWrapper" style="">
                            <p style=" bottom:0; left:0;" contentEditable="true">
                                <?=$export->streamship_line;?>
                            </p>
                        </div>
                    </td>
                    <td width="9%"><b>Terminal:</b></td>
                    <td width="19%" class="line under">
                        <div id="textArea" class="textAreaWrapper" style="">
                            <p style=" bottom:0; left:0;" contentEditable="true">
                                <?=$export->terminal;?>
                            </p>
                        </div>
                    </td>
                    <td width="13%"><b>Container #:</b></td>
                    <td width="20%" class="line under">
                        <div id="textArea" class="textAreaWrapper" style="">
                            <p style=" bottom:0; left:0;" contentEditable="true">
                                <?=$export->container_number;?>
                            </p>
                        </div>
                    </td>

                </tr>
                </tbody>
            </table>

            <table class="spec1" width="100%">
                <tbody>
                <tr>
                    <th style="text-align:center;">AES INFORMATION</th>
                </tr>
                </tbody>
            </table>

            <table width="100%">
                <tbody>
                <tr>
                    <td width="17%"><b>ITN #:</b></td>
                    <td width="87%" class="line under">
                        <div id="textArea" class="textAreaWrapper" style="">
                            <p style=" bottom:0; left:0;" contentEditable="true">
                                <?=$export->itn;?>
                            </p>
                        </div>
                    </td>
                </tr>

                </tbody>
            </table>
            <p style="font-size:12px;">I certify under penalty of perjury under the laws of the United States of America
                ( Title 18 U.S.C. § 1001) that the foregoing is true and correct. Title 18 U.S.C. § 1001 prohibits
                making false statements, lying to or cancealing information from a federal official by oral affirmation,
                written statement or mere denial.</p>
            <table width="100%">
                <tbody>
                <tr>
                    <td width="27%"><b>AUTHORIZED SIGNATURE:</b></td>
                    <td width="43%" align="center" class="line under"></td>
                    <td width="7%"><b>Date:</b></td>
                    <td width="23%" align="center" class="line under"></td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align:center;">Exporter/Agent</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
