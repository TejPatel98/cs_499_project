@extends('layouts/main')

@section('content')

<div class="container">
	<div class="card">
		<div class="card-header">
			<h1>Portfolio Setup</h1>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col">
					{!! Form::open(array('url' => '/stock/submit')) !!}

					<div class="form-group">
						{!! Form::label('strategy', 'Strategy') !!}
						{!! Form::select('strategy', array('strategy_one' => 'Buy Call', 'strategy_two' => 'Buy Put'), ['class' => 'form-control']) !!}
					</div>

					<div class="form-group">
						{!! Form::label('scan', 'Scan') !!}
						{!! Form::select('scan', array('scan_one' => 'Scan One', 'scan_two' => 'Scan Two', 'scan_three' => 'Scan Three'), ['class' => 'form-control']) !!}
					</div>

					<div class="form-group">
						{!! Form::label('principal', 'Principal Amount') !!}
						{!! Form::number('principal', '', ['class' => 'form-control']) !!}
					</div>

					<div class="form-group">
						{!! Form::label('investment_percent', 'Investment Percent') !!}
						{!! Form::number('investment_percent', '', ['class' => 'form-control']) !!}
					</div>

					<div class="form-group">
						{!! Form::label('start_date', 'Start Date') !!}
						{!! Form::date('start_date', date('Y-m-d')) !!}  
						{!! Form::label('end_date', 'End Date') !!}
						{!! Form::date('end_date', date('Y-m-d')) !!}  
					</div>

					<button class="btn btn-success" type="submit">Submit</button>

					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
