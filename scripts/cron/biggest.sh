#!/bin/sh
#
# SccsId[] = "%W% %G% (List 'biggest' files in filesystem)"
#
#----------------------------------------------------------------------#
#                              biggest.sh                              #
# -------------------------------------------------------------------- #
#   Program documentation and notes located at the bottom of script.   #
#----------------------------------------------------------------------#

  #----------------------------------------------------------------#
  # Script_name assignment is necessary if there exists the        #
  # possibility that this process may be run by the 'at' command.  #
  # Run via 'at' and $0 simply returns '/bin/sh' or 'sh' (hardly   #
  # desirable if you run that into basename).                      #
  #----------------------------------------------------------------#
  script_name="biggest.sh"
  [ $0 = "/bin/sh" -o `dirname $0` = "." ] \
    && script_home=`pwd` || script_home=`dirname $0`
  bin=/usr/bin # Default

  #----------------------------------------------#
  # Use awk, nawk or gawk, depending on the OS.  #
  #----------------------------------------------#
  OZ=`uname -s 2> /dev/null | tr '[a-z]' '[A-Z]' 2> /dev/null`
  if   [ ."$OZ" = ."HP-UX" ]; then
     AWK=awk
  elif [ ."$OZ" = ."LINUX" ]; then
     bin_dir=/bin
     AWK=gawk
  elif [ ."$OZ" = ."SUNOS" ]; then
     AWK=nawk
  else # Unknown OS, see if there's any kind'a Awk available.
     if   [ -f $bin/gawk ]; then AWK=gawk
     elif [ -f $bin/nawk ]; then AWK=nawk
     elif [ -f $bin/awk  ]; then AWK=awk
     elif [ `expr "\`awk 2>&1\`" : 'Usage: '` -gt 0 ]; then AWK=awk
     else # This is really getting awkward :-o
        echo "Unable to locate [gn]awk program! $0 terminating." 1>&2
        exit 1 # Well behaved here
     fi
  fi


#======================================================================#
#                    L O C A L    F U N C T I O N S                    #
#                       (in alphabetical order)                        #
#----------------------------------------------------------------------#
EXIT_USAGE()
#----------------------------------------------------------------------#
{
  echo "Usage: biggest.sh -fHh -l <nn> -s <nnn> -t <dir> -v fs\n" 1>&2
  echo "                  -f = follow links"                      1>&2
  echo "                  -H = Full documentation"                1>&2
  echo "                  -h = Usage brief"                       1>&2
  echo "                  -l = Displays <nn> lines"               1>&2
  echo "                  -s = Minimum file size is <nnn>"        1>&2
  echo "                  -t = Temp/work directory, <dir>"        1>&2
  echo "                  -v = Edit (vi) file list"               1>&2
  echo "                  fs = Required filesystem argument."     1>&2
  echo ""                                                         1>&2
  exit 1
}

#----------------------------------------------------------------------#
SHOW_DOCUMENTATION() # Function documentation located at bottom.       #
#----------------------------------------------------------------------#
{
  #----------------------------------------------------------------#
  # If the following variables are not set, use these as defaults. #
  #----------------------------------------------------------------#
  : ${script_name:=`basename $0`}
  : ${script_home:=`dirname  $0`}
  SD_script_home=`echo $script_home | sed 's/\/*$/\//'`

  #------------------------------------------------#
  # User wants help, so find the documentation     #
  # section and print everything from there down.  #
  #------------------------------------------------#
  $AWK -v script_name=$script_name \
    'BEGIN { n=0 }

     { #------------------------------------------#
       # Until we find the documentation section, #
       # keep looking at each line.               #
       #------------------------------------------#
       if (n == 0)
       {
         if ($0 ~ /^# +D O C U M E N T A T I O N/)
         {
           n = NR
           print line
           print $0
         }
         else
         {
           line = $0
         }

         next
       }    #-------------------------------------#
       else # Once we find it, print until EOF.   #
       {    #-------------------------------------#
         print
       }
     }

     END {
           if (n == 0) # Means there is no documentation section.
           {
            "date +%Y-%m-%d" | getline yyyy_mm_dd
             print yyyy_mm_dd" NO DOCUMENTATION",
               "section found for "script_name".\a" | "cat 1>&2"
             exit 1 # Exit failure
           }
           exit 0 # Else exit success
         }' ${SD_script_home}$script_name

  exit $?
} # "SD_" prefix identifies this function's variables


#======================================================================#
#                     I N I T I A L I Z A T I O N                      #
#======================================================================#
  opt_v=0 # Default 'vi' option (0 = Do NOT vi the file list)
  tmp=/var/tmp
  follow=""
  size="499999" # Default minimum filesize
  lines="500"   # Default maximum lines

  while getopts fHhl:s:t:v opt 2> /dev/null
  do
     case "$opt" in
        f ) follow='-follow'  ;;
        H ) SHOW_DOCUMENTATION;;
        h ) EXIT_USAGE        ;;
        l ) lines="$OPTARG"   ;; # Max number of lines to display.
        s ) size="$OPTARG"    ;; # Minimum file size.
        t ) tmp="$OPTARG"     ;; # Temp directory (if /var/tmp full)
        v ) opt_v=1           ;;
        * ) echo "Ignoring invalid option, $1.";;
     esac
  done
  #----------------------------------#
  # Shift past options to arguments. #
  #----------------------------------#
  shift `expr $OPTIND - 1`


#======================================================================#
#                                M A I N                               #
#======================================================================#

  [ $# -eq 0 ] && EXIT_USAGE

  #-----------------------------------------------------------------#
  # Ensure we have write-access to temp/work directory.             #
  #-----------------------------------------------------------------#
  if [ ! -d $tmp ]; then
     echo "Temp/work directory, $tmp not found!" \
          "\n$script_name terminated."
     exit 1
  elif [ ! -w $tmp ]; then
     echo "No write access to temp/work directory, $tmp!" \
          "\n$script_name terminated."
     exit 1
  fi

  #----------------------------------------------------------------#
  # File lists of remote filesystems is problematic, so we limit   #
  # our operations to local filesystems only.                      #
  #----------------------------------------------------------------#
  df -lk $1
  if [ $? -ne 0 ]; then
     echo "$1 MUST be a local filesystem--it is not!" \
          "\n$script_name terminated."
     exit 1
  fi

  #----------------------------------------------------------------#
  # Build a 'find' command with the necessary options/arguments.   #
  # Be sure to exclude anything with cdrom in it and include -xdev #
  # -xdev if the filesystem being searched is root (/).            #
  #----------------------------------------------------------------#
  outfile=$tmp/$LOGNAME"_biggest.files" # Formatted 'find' output
  include='-size +'"$size"'c -exec ls -lc {} \;'
  exclude='-o -fstype nfs -prune -o -name cdrom\* -prune'
  [ ."$1" = ."/" ] && find_opt="-xdev $follow" || find_opt="$follow"

  date "+%D %T"
  find_cmd="find $1 $find_opt $include $exclude -print"

  #----------------------------------------------------------------#
  # Display find command before running it.  Use [gn]awk to format #
  # the output and sort it in descending order (biggest on top).   #
  #----------------------------------------------------------------#
  echo "$find_cmd 2> /dev/null | $AWK"
  eval  $find_cmd 2> /dev/null | $AWK \
    'BEGIN \
     {
       i   = 0
       own = 3
       siz = 5
       mmm = 6
       day = 7
       yyy = 8 # This may actually be yyyy or hh:mi
       Mon = "^(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)$"
     }
  # "! /^[bcd]/" skips block and character devices and directories
     ! /^[bcd]/ \
     {
       #-----------------------------------------------------------#
       # If it looks like owner and group fields are concatonated, #
       # try backing up the field ($n) list and work with that.    #
       #-----------------------------------------------------------#
       if ($siz !~ /[0-9]+/ && !match($mmm,Mon))
       {
         if ($(siz-1) ~ /[0-9]+/ && match($(mmm-1),Mon))
         {
           siz = 4 # 4th field
           mmm = 5 # Etc.
           day = 6
           yyy = 7
         }
       }

       gsub(/[\t ]+/," ")  # Squeeze whitespace.
       gsub(/./,"& ",$siz) # Isolate each digit,
       q=split($siz,a," ") #   then split the $siz into an array.
       $siz=""             # Clear $siz.
       for (p=1;q>0;q--)   # Insert commas into $siz.
       {
         $siz=a[q]""$siz
         if ((p%3) == 0 && q != 1) $siz=","$siz # Insert commas here
         p++
       }

       printf("%13s %-8s %s %02d %-5s %s\n",
         $siz, $own, $mmm, $day, $yyy, $NF)

       #-----------------------------------------------#
       # if size value is not 5, then reset it, et al. #
       #-----------------------------------------------#
       if (siz != 5)
       {
         siz = 5
         mmm = 6
         day = 7
         yyy = 8
       }
     }' |  sort -r -k 1,2 | head -$lines  > $outfile

  #----------------------------------------------------------------#
  # Unless 'vi' option was given, simply cat our file list.        #
  #----------------------------------------------------------------#
  if [ `wc -l < $outfile` -eq 0 ]; then
     echo "No files found in $1 > $size bytes in size."
  else
     [ $opt_v -eq 0 ] && cat $outfile || vi $outfile
  fi

  exit $?


#======================================================================#
#                      D O C U M E N T A T I O N                       #
#======================================================================#
#                                                                      #
#      Author: Bob Orlando (Bob@OrlandoKuntao.com)                     #
#                                                                      #
#        Date: April 8, 1995                                           #
#                                                                      #
#  Program ID: biggest.sh                                              #
#                                                                      #
# Code Contrl: aphrodite:~dmc/SCCS.                                    #
#                                                                      #
#       Usage: biggest.sh -fHh -l <nn> -v -t <dir> -s <nnn> fs         #
#                                                                      #
#                         -f = Follow links                            #
#                         -H = Displays detailed documentation         #
#                         -h = Provides usage brief                    #
#                         -l = Displays <nn> lines (default is 500)    #
#                         -s = Minimum file size is <nnn>              #
#                              (default is 500K)                       #
#                         -t = Use <dir> as temp/work directory        #
#                              (default is /var/tmp)                   #
#                         -v = Edit (vi) file list                     #
#                         fs = Required filesystem argument.           #
#                                                                      #
#     Purpose: List biggest files in a given filesystem (files         #
#              appear in descending order).                            #
#                                                                      #
# Description: Using the find command, descend through the specified   #
#              file system (fs) listing all files whose sizes exceed   #
#              either the default minimum size (500K) or the minimum   #
#              value provided via size (-s) option.  The filelist      #
#              is created in /var/tmp by default as it usually much    #
#              larger than /tmp.  However, in the event that /var      #
#              is the filesystem that's full (or is not writable to    #
#              the user), the temp dir (-t) option is available to     #
#              redirect the output elsewhere.                          #
#                                                                      #
#              When root is the directory being searched, -xdev is     #
#              supplied as a find argument so only root, and none      #
#              of its subdirectories, is searched.                     #
#                                                                      #
#              With the 'vi' option (-v) the user can edit the         #
#              normally cat'd file list.                               #
#                                                                      #
#    Modified: 2005-03-02 Bob Orlando                                  #
#                v1.6   * Add code to parse the correct fields when    #
#                         the owner and group fields are concatonated, #
#                         effectively making two fields, one (really   #
#                         messes up AWK processing).                   #
#                                                                      #
#----------------------------------------------------------------------#
