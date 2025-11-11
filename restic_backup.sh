#!/bin/bash


# REM Backing up to both USA and EU
start=$(date +%s)
mfdate=$(date +"%FT%H%M")
year=$(date +"%Y")
month=$(date +"%b")

terminal-notifier -group rbu -message "Starting Restic Backups to USA" -title "Restic Backups"
echo "===Starting Restic USA Backup for @Work / @@RobertPowell / User" >> /Users/robertpowell/ResticBackups/logs/$year/$month/ResticLog_$mfdate.txt
echo "===Version Jul 2025 - Backblaze using S3 credentials===" >> /Users/robertpowell/ResticBackups/logs/$year/$month/ResticLog_$mfdate.txt
source /Users/robertpowell/ResticBackups/secrets/B2_S3.env
echo "US Env Loaded" > /Users/robertpowell/ResticBackups/logs/$year/$month/ResticLog_$mfdate.txt


# Grab CPU and memory usage & # Send notification
USAGE=$(ps -p $$ -o %cpu,%mem)
terminal-notifier -title "Script Resource Usage" -message "CPU & MEM: $USAGE"

terminal-notifier -group rbu -message "USA = Starting @Work" -title "Restic Backups"
echo "===Start of @Work to US" >> /Users/robertpowell/ResticBackups/logs/$year/$month/ResticLog_$mfdate.txt
restic -r s3:s3.us-west-000.backblazeb2.com/RoPoBackup2025wk -v -p ~/ResticBackups/secrets/repo_pw_1.txt backup /Users/robertpowell/Library/CloudStorage/Dropbox/@Work/ >> /Users/robertpowell/ResticBackups/logs/$year/$month/ResticLog_$mfdate.txt
echo "===End of @Work to US" >> /Users/robertpowell/ResticBackups/logs/$year/$month/ResticLog_$mfdate.txt

USAGE=$(ps -p $$ -o %cpu,%mem)
terminal-notifier -title "Script Resource Usage" -message "CPU & MEM: $USAGE"

terminal-notifier -group rbu -message "USA = Finished @Work ~ Starting robertpowell" -title "Restic Backups"
echo "===Start of @@RobertPowell to US" >> /Users/robertpowell/ResticBackups/logs/$year/$month/ResticLog_$mfdate.txt
restic -r s3:s3.us-west-000.backblazeb2.com/RoPoBackup2025rp -v -p ~/ResticBackups/secrets/repo_pw_2.txt backup /Users/robertpowell/Library/CloudStorage/Dropbox/@@RobertPowell/ >> /Users/robertpowell/ResticBackups/logs/$year/$month/ResticLog_$mfdate.txt
echo "===End of @@RobertPowell to US" >> /Users/robertpowell/ResticBackups/logs/$year/$month/ResticLog_$mfdate.txt
terminal-notifier -group rbu -message "USA = Finished robertpowell" -title "Restic Backups"

USAGE=$(ps -p $$ -o %cpu,%mem)
terminal-notifier -title "Script Resource Usage" -message "CPU & MEM: $USAGE"

terminal-notifier -group rbu -message "USA = Finished @robertpopowell ~Starting Users" -title "Restic Backups"
echo "===Start of User Folder with exclusions to US" >> /Users/robertpowell/ResticBackups/logs/$year/$month/ResticLog_$mfdate.txt
restic -r s3:s3.us-west-000.backblazeb2.com/RoPoBackup2025iMacUser -v -p ~/ResticBackups/secrets/repo_pw_3.txt backup /Users/robertpowell/ --exclude-file=/Users/robertpowell/ResticBackups/excludes/excludesrepo8.txt >> /Users/robertpowell/ResticBackups/logs/$year/$month/ResticLog_$mfdate.txt
echo "===End of Users US Backup" >> /Users/robertpowell/ResticBackups/logs/$year/$month/ResticLog_$mfdate.txt

#Add European backup here.

terminal-notifier -group rbu -message "Finished Users and USA backups ~ Starting Restic Backups to EU" -title "Restic Backups"
echo "===Starting Restic EU Backup for @Work / @@RobertPowell / User" >> /Users/robertpowell/ResticBackups/logs/$year/$month/ResticLog_$mfdate.txt
source /Users/robertpowell/ResticBackups/secrets/B2_S3_EU.env
echo "EU Env Loaded" >> /Users/robertpowell/ResticBackups/logs/$year/$month/ResticLog_$mfdate.txt
terminal-notifier -group rbu -message "EU = Starting @Work" -title "Restic Backups"
echo "===Start of @Work to EU" >> /Users/robertpowell/ResticBackups/logs/$year/$month/ResticLog_$mfdate.txt

restic -r s3:s3.eu-central-003.backblazeb2.com/EUBackup2025work -v -p ~/ResticBackups/secrets/repo_pw_1.txt backup /Users/robertpowell/Library/CloudStorage/Dropbox/@Work/ >> /Users/robertpowell/ResticBackups/logs/$year/$month/ResticLog_$mfdate.txt
echo "===End of @Work to EU" >> /Users/robertpowell/ResticBackups/logs/$year/$month/ResticLog_$mfdate.txt

terminal-notifier -group rbu -message "EU = Finished @Work ~ Starting robertpowell" -title "Restic Backups"
echo "===Start of @@RobertPowell to EU" >> /Users/robertpowell/ResticBackups/logs/$year/$month/ResticLog_$mfdate.txt
restic -r s3:s3.eu-central-003.backblazeb2.com/EuBackup2025robertpowell -v -p ~/ResticBackups/secrets/repo_pw_2.txt backup /Users/robertpowell/Library/CloudStorage/Dropbox/@@RobertPowell/ >> /Users/robertpowell/ResticBackups/logs/$year/$month/ResticLog_$mfdate.txt
echo "===End of @@RobertPowell to EU" >> /Users/robertpowell/ResticBackups/logs/$year/$month/ResticLog_$mfdate.txt


terminal-notifier -group rbu -message "Finished @Work Starting Users" -title "Restic Backups"
echo "===Start of User Folder with exclusions to EU" >> /Users/robertpowell/ResticBackups/logs/$year/$month/ResticLog_$mfdate.txt
restic -r s3:s3.eu-central-003.backblazeb2.com/EuBackup2025iMacUser -v -p ~/ResticBackups/secrets/repo_pw_3.txt backup /Users/robertpowell/ --exclude-file=/Users/robertpowell/ResticBackups/excludes/excludesrepo8.txt >> /Users/robertpowell/ResticBackups/logs/$year/$month/ResticLog_$mfdate.txt
echo "===End of Users EU Backup" >> /Users/robertpowell/ResticBackups/logs/$year/$month/ResticLog_$mfdate.txt
terminal-notifier -group rbu -message "EU = Finished Users and EU backups" -title "Restic Backups"

# elapsed time calculation
end=$(date +%s)
echo "===This Backup took: $((end - start))  seconds" >> /Users/robertpowell/ResticBackups/logs/$year/$month/ResticLog_$mfdate.txt
terminal-notifier -group rbu -sound default -message "End of Backups - This backup took $((end - start)) seconds to complete" -title "Restic Backups" -open "file:///Users/robertpowell/ResticBackups/logs/$year/$month/ResticLog_$mfdate.txt"
echo "===End of Backups===" >> /Users/robertpowell/ResticBackups/logs/$year/$month/ResticLog_$mfdate.txt

# Read email body from file
BODY=$(cat /Users/robertpowell/ResticBackups/logs/$year/$month/ResticLog_$mfdate.txt)
osascript -e "
tell application \"Mail\"
    set newMessage to make new outgoing message with properties {subject:\"Backup Report at $mfdate\", content:\"$BODY\", sender:\"robert_powell@icloud.com\"}
    tell newMessage
        make new to recipient at end of to recipients with properties {address:\"backups@robertpowell.com\"}
    end tell
    send newMessage
end tell
"


RECIPIENT="+447970123407"
MESSAGE="$BODY"

# Escape quotes in the message
MESSAGE=$(echo "$MESSAGE" | sed 's/"/\\"/g')

echo "Sending iMessage to: $RECIPIENT"
echo "Message: $MESSAGE"

osascript -e "
tell application \"Messages\"
    send \"$MESSAGE\" to buddy \"$RECIPIENT\"
end tell

delay 10
tell application \"Mail\" to quit
"

if [ $? -eq 0 ]; then
    echo "Message sent successfully!"
else
    echo "Error: Failed to send message"
    exit 1
fi
