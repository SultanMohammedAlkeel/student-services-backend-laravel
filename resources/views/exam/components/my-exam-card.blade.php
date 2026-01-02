<div class="col-12 col-sm-12 col-md-6 col-lg-4 mb-5">
    <!-- card -->
    <div class="card bg-primary shadow-soft border-light book-card">
        <div class="card-header book-type">{!! $examType[$exam_data->type] !!}</div>
        <small class="text-center h6 custom-font">{{ $exam_data->type }}</small>
        <div class="card-body border-top border-light rtl-layout">
            <div>
                <p class="h5 custom-font text-behance">{{ $exam_data->name }}</p>
                <p class="h6 custom-font text-gray">لغة الاختبار: <span class="text-behance">{{ $exam_data->language }}</span></p>
                <p class="h6 custom-font text-gray">رفع بواسطة : <span class="text-behance">{{ $users->where('id', $exam_data->created_by)->first()->name }}</span> </p>
                <div class="size">عدد الاسئلة : <span class="text-behance">{{ count(json_decode($exam_data->exam_data, true)['exam'] ?? []) }}</span> </div>
                <h3 class="h6 font-weight-light text-gray mt-2 custom-font">{!! nl2br(e($exam_data->description)) !!}</h3>
            </div>
        </div>
        <div class="card-footer border-top border-light p-4">
            <div class="d-flex align-items-center">
                <div class="text-behance">النتيجة :{{ $record->where('exam_id',  $exam_data->id)->first()->score }}%</div>
                <div class="ml-auto">
                    <a href="{{ route('exam.list-students', [$exam_data->code]) }}" class="btn btn-primary" title="قائمة الطلاب">
                        <span aria-hidden="true" class="fa-solid fa-users" id="edit-icon-{{ $exam_data->id }}"></span>
                        <span class="save-count" id="edit-book-{{ $exam_data->id }}">{{ $records->where('exam_id', $exam_data->id)->count() }}</span>
                    </a>
                    <a href="{{ route('exam.show-my-exam', [$exam_data->code]) }}" class="btn btn-primary" title="فتح الاختبار">
                        <span aria-hidden="true" class="fa-solid fa-folder-open" id="open-icon-{{ $exam_data->id }}"></span>
                        <span class="save-count" id="open-book-{{ $exam_data->id }}">{{ $records->where('exam_id', $exam_data->id)->count() }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- card -->
</div>