import telepot
import sys
bot = telepot.Bot('your_telegram_bot_token')# got when you made your bot  
bot.sendMessage(sys.argv[1],str(sys.argv[2]))

