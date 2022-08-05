@foreach($notifications as $post)
<div>
	<h3><a href="">{{ $post->contenu }}</a></h3>
	<p>{{ $post->contenu }}</p>


	<div class="text-right">
		<button class="btn btn-success">Read More</button>
	</div>


	<hr style="margin-top:5px;">
</div>
@endforeach
