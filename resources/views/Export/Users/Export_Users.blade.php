
<table>
        <thead>
        <tr class="text-center">
            <th >NO</th>
            <th >ID DEPARTEMEN</th>
            <th >DEPARTEMEN</th>
            <th >ID DEPARTEMEN SUB</th>
            <th >DEPARTEMEN SUB</th>
            <th >POS</th>
            <th >ID GRADE</th>
            <th >GRADE</th>
            <th >ID ABSEN</th>
            <th >NIP</th>
            <th >NAME</th>
            <th >EMAIL</th>
            <th >NO HP</th>
            <th >ID SKEMA HARI KERJA</th>
            <th >SKEMA HARI KERJA</th>
            <th >JML HARI KERJA</th>
            <th >JAM KERJA</th>
            <th >TANGGAL BERGABUNG</th>
            <th >TANGGAL LAHIR</th>
            <th >USERNAME</th>
            <th >PASSWORD</th>
        </tr>
        </thead>
      
        <tbody>
        @php($i=0)

        @foreach($karyawan as $d)
            <tr>
                <td>{{ $i }}</td>
                <td>{{ $d->idDepartemen }}</td>
                <td>{{ $d->departemen }}</td>
                <td>{{ $d->idDepartemenSub }}</td>
                <td>{{ $d->subDepartemen }}</td>
                <td>{{ $d->pos }}</td>
                <td>{{ $d->id_grade }}</td>
                <td>{{ $d->grade }}</td>
                <td>{{ $d->idAbsen }}</td>
                <td>{{ $d->nik }}</td>
                <td>{{ $d->name }}</td>
                <td>{{ $d->email }}</td>
                <td>{{ $d->no_hp }}</td>
                <td>{{ $d->idSkemaHariKerja }}</td>
                <td>{{ $d->skemaHariKerja }}</td>
                <td>{{ $d->jmlHari }}</td>
                <td>{{ $d->jamkerja }}</td>
                <td>{{ $d->doj }}</td>
                <td>{{ $d->dob }}</td>
                <td>{{ $d->username }}</td>
                @if($userLogin[$i]==$d->nik)
                <td>{{ $userLogin[$i] }}</td>
                @else
                <td>Password Sudah Di Ganti Oleh User!!!</td>
                @endif
            </tr>
            @php($i++)  
        @endforeach
        
       
        </tbody>
    </table>