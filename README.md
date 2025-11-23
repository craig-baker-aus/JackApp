**JackApp Cashflow Forecasting API Technical Exercise**.

The code consists of one web page (index.php) which implements a very simplistic user interface. The interface focuses on exercising the functionality defined in cashFlowForecastingAPI.php. 
The code consists of a total of four .php files and one .js file. Each file contains comments describing its purpose.

In-memory data storage is used. An object oriented style has been used. The code was tested using the Visual Studio Code built-in server.

Release v1.0 of each code file should be used when running the code.

The user interface is self explanatory. Transactions can be added and deleted and displayed. Buttons are provided to get the current balance and to generate a basic cashflow forecast, 
as defined in the Months field. To simplify error handling, the Months field and recurring transaction fields will default to 2 if nothing is entered.

I have not used PHP for some time. I first had to install PHP (and WAMP) along with adding extensions to VS Code. The vast majority of time was spent in setting up all the relevant tools and debuggers. I have most recently been working with Microsoft Access Visual Basic
and VB.NET. A significant amount of time was also spent switching thought processes from those languages, as would be expected.

The solution is probably over engineered and I spent significantly more than 2-3 hours. While testing the setup of all the tools, I was using this exercise as my starting point. Initial development was a low priority an therefore slow but it lead to grand ideas which it then made sense to finish off.
In all I spent approximately 30 hours between Monday 17 November and Thursday 20 November to perform all the above tasks, including documentation.

Update: 24 November.

I had problems with Dates as happens with many languages. It was irritating, so I investigated a better approach.
The TypeImprovement branch introduces a new PHP type Date and a corresponding JavaScript type PHPDate. I also added some enumerated types.
The functionality is equivalent to Version 1.
