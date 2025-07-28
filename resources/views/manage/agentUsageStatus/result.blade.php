@extends('manage.layout')

@section('contents')

<header class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <h1 class="text-lg leading-6 font-semibold text-gray-900">
            代理店一覧画面
        </h1>
    </div>
</header>
<main>

    @include('msg')

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <form method="post" action="{{ route('manageAgentSearchResult') }}">
            @csrf
            <div class="flex">

                <div class="flex-initial px-4">
                    <label for="agentNo">販売店</label>
                    <select name="distributor_cd" id="distributor_cd" class="border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500" >
                        @foreach($distributorlist as $item)
						<option value="{{ $item["distributor_cd"] }}" @if ($item["distributor_cd"] == $distributor_cd) selected @endif>{{ $item["name"] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-initial px-4">
                    <label for="agentNo">代理店</label>
					<select name="agent_cd" id="agent_cd" class="border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                        <option value="0" data-val="">ーー</option>
                    	@foreach($agentcdlist as $item)
                        <option value="{{ $item["agent_cd"] }}" >{{ $item["name"] }}</option>
                    	@endforeach
                    </select>
                </div>

            </div>

				<br>

                <div class="flex-initial px-4">
                    <button type="submit" class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                       検索 
                    </button>
                </div>
            </div>
        </form>
    </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-0">
            <div class="text-right">
                <button type="button" id="btnAdd" onclick="location.href = '{{ route('manageAgentEdit') }}';"
                    class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                    代理店新規追加
                </button>
            </div>
        </div>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table id="userTable" class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-green-500">
                                    <tr>
                                        <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                            No
                                        </th>
                                        <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                            契約状況
                                        </th>
                                        <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                            代理店名 
                                        </th>
                                        <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-white border">
                                           郵便番号/住所 
                                        </th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-white border">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
									<br>
                                    @foreach( $result_sub as $item )
                                        <tr>
                                            <td class="px-2 py-4 whitespace-nowrap text-sm text-right font-medium border">
												{{ $item["id"] }}
                                            </td>
                                            <td class="px-3 py-4 w-45 text-sm font-medium border">
											@if ( $item["status"] == 0 )
												契約中
											@else
												解約
											@endif
                                            </td>
                                            <td class="px-3 py-4 w-45 text-sm font-medium border">
												<!-- <a href="/manage/agentEdit/{{$item["agent_cd"]}}" >{{ $item["name"] }}</a> -->
												<a href="{{ route('manageAgentEdit') }}/{{$item["agent_cd"]}}" >{{ $item["name"] }}</a>
                                            </td>
                                            <td class="px-2 py-4 whitespace-nowrap text-sm text-left font-medium border">
												{{ $item["postCode"] }} :  {{ $item["address"] }}
											</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-center font-medium border">
                                            <button type="button" onclick="location.href = '{{ route('manageAgentUsageStatus2') }}/{{$item["agent_cd"]}}';" class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                                                詳細
                                            </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


</main>

    <script>

/*
    function agentchange()
    {
        var distributor_cd = $('#distributor_cd').val();
        // console.log(distributor_cd);
        var agent_cd = document.getElementById("agent_cd");
        agent_cd.options.length = 0;

        var op = document.createElement("option");
        op.value = 0;
        op.text = "ーー";
        agent_cd.appendChild(op);
        for (let i = 0; i < agentid_sub.length; i++) {
            // console.log(agentid_sub2[i]);
            if ( agentid_sub[i].agentid === distributor_cd) {
                var op = document.createElement("option");
                value = agentid_sub[i];
                op.value = value.id;
                op.text = value.label;
                agent_cd.appendChild(op);
            }
        }
    }
//

    var agentid_sub = new Array();
    agentid_sub[""] = {id:"0", label:"ーー"};

    @foreach($agentcdlist as $item)
        agentid_sub["{{ $loop->index }}"] =
            {agentid:"{{ $item["distributor_cd"] }}", id:"{{ $item["agent_cd"] }}", label:"{{ $item["name"] }}" };
    @endforeach
    console.log(agentid_sub);
*/
    </script>

@endsection
