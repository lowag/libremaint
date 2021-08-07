function Calculate(Luhn)
 {
    var sum = 0;
    for (i=0; i<Luhn.length; i++ )
    {
        sum += parseInt(Luhn.substring(i,i+1));
    }
    var delta = new Array (0,1,2,3,4,-4,-3,-2,-1,0);
    for (i=Luhn.length-1; i>=0; i-=2 )
    {       
        var deltaIndex = parseInt(Luhn.substring(i,i+1));
        var deltaValue = delta[deltaIndex];   
        sum += deltaValue;
    }   
    var mod10 = sum % 10;
    mod10 = 10 - mod10;   
    if (mod10==10)
    {       
        mod10=0;
    }
    return mod10;
 }
 
  function Validate(Luhn)
 {
    var LuhnDigit = parseInt(Luhn.substring(Luhn.length-1,Luhn.length));
    var LuhnLess = Luhn.substring(0,Luhn.length-1);
    if (Calculate(LuhnLess)==parseInt(LuhnDigit))
    {
        return true;
    }   
    return false;
 }
