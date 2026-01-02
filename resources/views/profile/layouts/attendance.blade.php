<div class="card-header text-center pb-0">
    <h2 class="h4 custom-font">سجل الحضور</h2>
</div> <!-- إغلاق div.card-header -->
<div class="col-12 col-md-12 col-lg-8 mx-auto mb-2">
    <div class="card card-sm card-body bg-primary border-light mb-0">
        <a href="#panel-2" data-target="#panel-2" class="accordion-panel-header" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="panel-1">
            <span class="icon-title h6 mb-0 font-weight-bold custom-font"><span class="fa-solid fa-person-chalkboard"></span>عرض سجل الحضور</span>
            <span class="icon"><span class="fas fa-plus"></span></span>
        </a>
        <div class="collapse" id="panel-2">
            <div class="pt-3">
                <nav>
                    <div class="nav nav-pills nav-fill flex-column flex-sm-row" id="nav-tab-ecommerce" role="tablist">
                        @foreach ($schedules as $i => $row)
                        <a class="nav-item nav-link m-1" id="department-card-{{ $i }}-tab" data-toggle="tab" href="#department-card-{{ $i }}" role="tab" aria-controls="department-card-{{ $i }}" aria-selected="false">
                            {{ $teacher->where('id', $row['teacher'])->first()->name }} - (<span class="text-info">{{ $courses->where('id', $row['course'])->first()->name }}</span>)
                        </a>
                        @endforeach
                    </div>
                </nav>
                <div class="tab-content mt-4 mt-lg-5" id="nav-tabContent-ecommerce">
                    @foreach ($schedules as $i => $row)
                    <div class="tab-pane fade" id="department-card-{{ $i }}" role="tabpanel" aria-labelledby="department-card-{{ $i }}-tab">
                        <table class="table table-hover shadow-inset rounded rtl-layout">
                            <tr>
                                <th class="border-0 text-center" scope="col" rowspan="2">#</th>
                                <th class="border-0 text-center" scope="col" rowspan="2">المحاضرة</th>
                                <th class="border-0 text-center" scope="col" rowspan="2">تاريخ المحاضرة</th>
                                <th class="border-0 text-left" scope="col" rowspan="2">الحالة</th>
                            </tr>
                            <tbody id="tbody">
                                @php
                                $index = 0;
                                @endphp
                                @foreach ($record as $j => $item)
                                @if ($item['teacher_id'] == $row['teacher'])
                                <tr>
                                    <th scope="row">{{ ++$index }}</th>
                                    <td class="text-center">المحاضرة رقم ({{ $item['lecture_number'] }})</td>
                                    <td class="text-center">{{ $item['lecture_date']  }}</td>
                                    <td class="text-left">
                                        @if ($item['status'] == 1)
                                        <span class="badge badge-success">حاضر</span>
                                        @else
                                        <span class="badge badge-danger">غائب</span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>