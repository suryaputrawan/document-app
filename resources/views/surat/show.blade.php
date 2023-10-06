<!DOCTYPE html>
<head>
    <title>{{ $data->jenis->nama }}</title>
    <meta charset="utf-8">
    <style>
      #halaman{
        font-size: 12pt;
        width: auto; 
        height: auto; 
        position: absolute; 
        padding-top: 80px; 
        padding-left: 30px; 
        padding-right: 30px; 
        padding-bottom: 30px;
      }
    </style>
</head>

<body>
    <div id=halaman>
        <table style="margin-left:auto;margin-right:auto;" width="100%" cellspcing="0">
            <tr>
                <td align="center" style="font-size: 20px"><u><b>{{ $data->jenis->nama }}</b></u></td>
            </tr>
            <tr>
                <td align="center" style="font-size: 14px">No. {{ $data->no_surat }}</td>
            </tr>
        </table>
        <p>Yang bertanda tangan di bawah ini:
        <table width="100%" cellspacing="0">
            <tr>
                <td>Nama </td>
                <td>: </td>
                <td>{{ $data->diajukanOleh->nama }}</td>
            </tr>
            <tr>
                <td>NIP </td>
                <td>: </td>
                <td>{{ $data->diajukanOleh->nip }}</td>
            </tr>
            <tr>
                <td>Jabatan </td>
                <td>: </td>
                <td>{{ $data->diajukanOleh->jabatan }}</td>
            </tr>
        </table>

        {!! $data->body !!}

        <br><p>Bali, {{ \Carbon\Carbon::parse($data->tgl_surat)->format('d M Y') }}</p><br>
        
        {{-- Tanda Tangan --}}
        <table style="margin-left:auto;margin-right:auto;font-size: 10pt;" cellspacing="0" cellpadding="7">
            <tr>
                <td align="center" colspan="2">Pihak Pengirim</td>
                <td align="center" colspan="{{ $recipient->count() }}">Pihak Penerima</td>
            </tr>
            <tr>
                <td align="center">Diajukan oleh</td>
                <td align="center">Disetujui oleh</td>
                <td align="center" colspan="{{ $recipient->count() }}"></td>
            </tr>
            <tr>
                <td align="center">
                    @if ($data->diajukanOleh->ttd_picture && $data->status_pengirim_diajukan == '1')
                        <img src="{{ $data->diajukanOleh->takeTtd }}" alt="tanda-tangan" height="65">
                    @else
                        <br><br>
                    @endif
                </td>
                <td align="center">
                    @if ($data->disetujuiOleh->ttd_picture && $data->status_pengirim_disetujui == '1')
                        <img src="{{ $data->disetujuiOleh->takeTtd }}" alt="tanda-tangan" height="65">
                    @else
                        <br><br>
                    @endif
                </td>
                @foreach ($recipient as $item)  
                    <td align="center">
                        @if ($item->ttd_picture != null && $item->pivot->status_recipient == 1)
                            <img src="storage/{{ $item->ttd_picture }}" alt="tanda-tangan" height="65">
                        @else
                           <br><br>
                        @endif
                    </td>  
                @endforeach
            </tr>
            <tr>
                <td align="center">
                    <u>{{ $data->diajukanOleh->nama }}</u><br>
                    {{ $data->diajukanOleh->jabatan }}
                </td>
                <td align="center">
                    <u>{{ $data->disetujuiOleh->nama }}</u><br>
                    {{ $data->disetujuiOleh->jabatan }}
                </td>
                @foreach ($recipient as $item)  
                    <td align="center">
                        <u>{{ $item->nama }}</u><br>
                        {{ $item->jabatan }}
                    </td>  
                @endforeach
            </tr>
        </table>

        <br><br>
        {{-- Tanda Tangan Menyetujui --}}
        <table style="margin-left:auto;margin-right:auto;font-size: 10pt" cellspacing="0" cellpadding="7">
            <tr>
                <td align="center" colspan="{{ $approval->count() }}">Disetujui Oleh,</td>
            </tr>
            <tr>
                @foreach ($approval as $item)
                {{-- <h6>{{ $item }}</h6>            --}}
                    <td align="center">
                        @if ($item->ttd_picture != null && $item->pivot->status_approval == 1)
                            <img src="storage/{{ $item->ttd_picture }}" alt="tanda-tangan" height="65">
                        @else
                            <br><br>
                        @endif
                    </td>  
                @endforeach
            </tr>
            <tr>
                @foreach ($approval as $item)  
                    <td align="center">
                        <u>{{ $item->nama }}</u><br>
                        {{ $item->jabatan }}
                    </td>  
                @endforeach
            </tr>
        </table>
    </div>
</body>

</html>