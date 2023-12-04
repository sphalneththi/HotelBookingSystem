function checkForm(){
	var name = document.getElementById("name").value;
	var mail = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
	const checkIn = new Date(document.getElementById("inDate").value);
	var checkOut = new Date(document.getElementById("outDate").value);
	const currentDate = new Date();
	var procode = document.getElementById("promo").value;
	var promocodeValue = ["SAVE10", "chris20", "Xmas20"];
	
	
	if(!name)
	{
		alert("Please enter your name");
		return false;
	}
	if(!document.getElementById("email").value.match(mail))
	{
		alert("Please enter valid email");
		return false;
	}
	if (checkIn < currentDate)
	{
		alert("Please select a check-in date that is after today");
		return false;
	}
	if (checkOut <= checkIn) 
	{
		alert("Please select a check-out date that is after the check-in date");
		return false;
	}
	if(procode != "")
	{
		if(!procode.includes(promocodeValue))
		{
			alert("Promo code not vaild");
			return false;
		}
	}
}
	
