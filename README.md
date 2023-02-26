# PlusNet DoS Log Parser

This PHP script reads a log file and extracts information related to Denial of Service (DoS) attacks. The script then uses an external API to gather additional information about the source of the attack, such as the region, country, city, postal code, and coordinates.

When the script is run, it will build an HTML table on the screen with the following information:

- Date & Time
- Source Attacker
- Region
- Country
- City
- ZIP/Postal Code
- Coordinates
## Requirements

- PHP 7.0 or higher
- A PlusNet event log named `logs.csv` placed in the same directory as the script

## How to Use

1. Clone or download the repository to your local machine
2. Add your PlusNet event log to the project directory and name it `logs.csv`
3. Run the script using PHP
4. The script will generate a HTML table with information about DoS attacks in the log

## Disclaimer

This script was created for personal use and is not a representation of high-quality work. The API used to gather location data has limits on free access, so data may not be complete or up-to-date. 

Use this script at your own risk and make sure to follow PlusNet's guidelines and policies.

## Notes

- The script currently only extracts information related to DoS attacks. To extract other types of information, modify the `str_contains()` function in the script.
- The script uses the `sleep()` function to delay requests to the external API. This is necessary to avoid exceeding the rate limit of the API. If you have a premium API key that allows for more requests, you can modify or remove the `sleep()` function to speed up the script.
- If you encounter any issues with the script, please open an issue on the GitHub repository.
