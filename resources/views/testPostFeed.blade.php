<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Demo add feed</title>
</head>
<body>
	<form action = "{{route('feed.store')}}" method = "POST" enctype="multipart/form-data">
		<input type="hidden" name="_token" value ="{{csrf_token()}}">
		Title: <input type="text" name = "title">
		<input type="hidden" name="memberId" value = "Aliquid quia odio et pariatur facere. Eius provident quia id totam quia. Sed minima officia iste ali">
		School: <input type ="text" name = "school">
		Link Face: <input type="text" name="linkFace">
		Type: <input type="text" name="type">
		<input type="hidden" name="totalFile" value = "2">
		File0: <input type="file" name="file_0">
		File1: <input type="file" name="file_1">

		<input type="submit" name="submit" value = "Gui">
	</form>
</body>
</html>