@include('user.bulkSearch.add',[
                'title' => 'CSV一括検索アップロード画面',
                'formTitle' => 'CSV一括検索データ',
                'routeName' => 'userCsvBulkSearchUpload',
                'notes' => config('note.csvBulkSearch.upload.note')
            ]
        )