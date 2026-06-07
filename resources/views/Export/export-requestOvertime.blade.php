
<table>
    <thead>
        <tr class="text-center">
            <th>NO</th>
            <th>ID OVERTIME</th>
            <th>DEPARTEMEN</th>
            <th>SUB DEPARTEMEN</th>
            <th>GRADE</th>
            <th>NIK</th>
            <th>NAMA</th>
            <th>TANGGAL PENGAJUAN</th>
            <th>TANGGAL LEMBUR</th>
            <th>TOTAL JAM LEMBUR</th>
            <th>ESTIMASI MULAI</th>
            <th>ESTIMASI SELESAI</th>
            <th>JAM JADWAL MASUK</th>
            <th>JAM JADWAL PULANG</th>
            <th>JAM ABSEN MASUK</th>
            <th>JAM ABSEN PULANG</th>
            <th>LIST ABSENSI</th>
            <th>KETERANGAN</th>
            <th>STATUS</th>
        </tr>
    </thead>
      
    <tbody>
        @php($i=1)
        @foreach($data as $d)
            <tr>
                <td>{{ $i }}</td>
                <td>{{ $d->id_overtime }}</td>
                <td>{{ $d->departemen }}</td>
                <td>{{ $d->sub_departemen }}</td>
                <td>{{ $d->grade }}</td>
                <td>{{ $d->nik }}</td>
                <td>{{ $d->name }}</td>
                <td>{{ $d->tgl_pengajuan }}</td>
                <td>{{ $d->tgl_lembur }}</td>
                <td>{{ $d->jam_lembur }}</td>
                <td>{{ $d->jam_awal }}</td>
                <td>{{ $d->jam_akhir }}</td>

                <td>{{ $d->jam_jadwal_masuk }}</td>
                <td>{{ $d->jam_jadwal_pulang }}</td>

                <td>{{ $d->jam_absen_masuk }}</td>
                <td>{{ $d->jam_absen_pulang }}</td>
                <td>{{ $d->jam_absen_jsonObject }}</td>
                <td>{{ $d->keterangan }}</td>
                @if($d->status==0)
                    <td>PENDING</td>
                @elseif($d->status==1)
                    <td>APPROVE</td>
                @elseif($d->status==2)
                    <td>REJECT</td>
                @else
                    <td>ERROR</td>
                @endif
            </tr>
            @php($i++)  
        @endforeach
    </tbody>
</table>