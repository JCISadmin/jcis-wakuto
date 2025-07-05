@extends('manage.layout')

@section('contents')

<header class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <h1 class="text-lg leading-6 font-semibold text-gray-900">
            代理店検索画面
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
                    <select name="distributor_cd" id="distributor_cd" class="border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500" onchange="agentchange()">
                        <option value="0" >ーー</option>
                        @foreach($distributorlist as $item)
                        <option value="{{ $item["distributor_cd"] }}" >{{ $item["name"] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-initial px-4">
                    <label for="agentNo">代理店</label>
                    <select name="agent_cd" id="agent_cd" class="border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500">
                        <option value="0" data-val="">ーー</option>
                    </select>
                </div>
            </div>
				<br>

                <div class="flex-initial px-4">
                    <button type="submit" class="px-6 py-2 justify-center border border-transparent rounded-md shadow-sm font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                       検索 
                    </button>
                </div>
		</form>
    </div>

</main>

	<script>

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
			// console.log(agentid_sub[i]);
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
	</script>

@endsection
