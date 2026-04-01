<style>
    .line_under {
        border-bottom-width: 1px;
        border-bottom-style: solid;
        border-bottom-color: #000;
        font-family: "Courier New", Courier, monospace;
    }
</style>
<div id="modal_content">
    <div class="non_haz" contenteditable="true">
        <table width="100%">
            <tbody>
            <tr>
                <td align="center">
                    <img src="{{ url('images/logo.jpg') }}" height="72" alt="{{ env('APP_NAME') }} Logo">
                </td>
            </tr>
            </tbody>
        </table>
        <br>
        <table width="100%">
            <tbody>
            <tr>
                <th id="impa">NON-HAZARDOUS DECLARATION</th>
            </tr>
            </tbody>
        </table>

        <br>
        <table width="100%" border="1">
            <tbody>
            <tr>
                <td width="33%">CARRIER</td>
                <td align="center" width="67%">{{ $export->streamship_line }}</td>
            </tr>
            <tr>
                <td>VESSEL NAME / VOYAGE</td>
                <td align="center">{{ $export->vessel . '  ' . $export->voyage }}</td>
            </tr>
            <tr>
                <td>ORIGIN</td>
                <td align="center">{{ $export->port_of_loading }}</td>
            </tr>
            <tr>
                <td>DESTINATION</td>
                <td align="center">{{ $export->destination }}</td>
            </tr>
            <tr>
                <td>BOOKING NUMBER</td>
                <td align="center">{{ $export->booking_number }}</td>
            </tr>
            <tr>
                <td>CONTAINER NUMBER</td>
                <td align="center">{{ $export->container_number }}</td>
            </tr>
            <tr>
                <td>NUMBER OF VEHICLES</td>
                <td align="center">{{ $export->vehicles->count() }}</td>
            </tr>
            </tbody>
        </table>
        <br>
        <br>
        <table width="100%">
            <tbody>
            <tr>
                <td align="left">THIS IS TO CERTIFY THAT ALL VEHICLES INCLUDED IN
                    THIS CONTAINER HAVE BEEN COMPLETELY DRAINED
                    OF FUEL AND RUN UNTIL STALLED. BATTERIES ARE
                    DISCONNECTED AND TAPED BACK AND ARE PROPERLY
                    SECURED TO PREVENT MOVEMENT IN ANY DIRECTION.
                    NO UNDECLARED HAZARDOUS MATERIALS ARE
                    CONTAINERIZED, SECURED TO, OR STOWED IN THIS
                    VEHICLE.<br>
                    WITH THE ABOVE STATEMENT, THESE VEHICLES ARE
                    CLASSIFIED AS NON-HAZARDOUS.
                </td>
            </tr>
            </tbody>
        </table>
        <br>
        <table width="100%">
            <tbody>
            <tr>
                <td width="11%">SIGNED</td>
                <td width="46%" align="center" class="line_under">

                </td>
                <td width="8%">DATE</td>
                <td width="35%" align="center" class="line_under"></td>
            </tr>

            </tbody>
        </table>
    </div>


</div>