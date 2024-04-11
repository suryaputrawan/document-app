<!DOCTYPE html>
<head>
    <title>{{ $data->jenis->nama }}</title>
    <meta charset="utf-8">
    <style>
      #halaman{
        font-size: 12pt;
        width: auto; 
        height: auto; 
        /* position: absolute;  */
        padding-top: 100px; 
        padding-left: 30px; 
        padding-right: 30px; 
        padding-bottom: 30px;
      }

    .custom-table {
        width: 100%;
    }

    .custom-table th, .custom-table td {
        border: none;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    table, th, td {
        border: 1px solid black;
    }

    
    </style>
</head>

<body>
    <div id=halaman>
        <table class="custom-table" style="margin-left:auto;margin-right:auto;border:none" width="100%" cellspcing="0">
            <tr>
                <td align="center" style="font-size: 20px"><u><b>{{ $data->jenis->nama }}</b></u></td>
            </tr>
            <tr>
                <td align="center" style="font-size: 14px">No. _________________________</td>
            </tr>
        </table>

        {!! $data->template !!}

        <p style="font-size: 11pt">Bali, {{ \Carbon\Carbon::now()->format('d M Y') }}</p>
        
        {{-- Tanda Tangan --}}
        <table class="custom-table" style="margin-left:auto;margin-right:auto;font-size: 10pt;border:none" cellspacing="0" cellpadding="7">
            <tr>
                <td align="center" colspan="2">Pihak Pengirim</td>
                <td align="center" colspan="2">Pihak Penerima</td>
            </tr>
            <tr>
                <td align="center">Diajukan oleh</td>
                <td align="center">Disetujui oleh</td>
                <td align="center" colspan="2"></td>
            </tr>
            <tr>
                <td align="center">
                    <br><br>
                </td>
                <td align="center">
                    <br><br>
                </td>
                <td align="center">
                    <br><br>
                </td>
                <td align="center">
                    <br><br>
                </td>
            </tr>
            <tr>
                <td align="center">
                    <u>__________________</u><br>
                    jabatan
                </td>
                <td align="center">
                    <u>__________________</u><br>
                    jabatan
                </td>
                <td align="center">
                    <u>__________________</u><br>
                    jabatan
                </td>
                <td align="center">
                    <u>__________________</u><br>
                    jabatan
                </td>
            </tr>
        </table>

        <br><br>
        {{-- Tanda Tangan Menyetujui --}}
        <table class="custom-table" style="margin-left:auto;margin-right:auto;font-size: 10pt;border:none" cellspacing="0" cellpadding="7">
            <tr>
                <td align="center" colspan="2">Disetujui Oleh,</td>
            </tr>
            <tr>
                <td align="center">
                    <br><br>
                </td>  
                <td align="center">
                    <br><br>
                </td>
            </tr>
            <tr>
                <td align="center">
                    <u>________________</u><br>
                    jabatan
                </td> 
                <td align="center">
                    <u>________________</u><br>
                    jabatan
                </td> 
            </tr>
        </table>
    </div>
</body>

</html>