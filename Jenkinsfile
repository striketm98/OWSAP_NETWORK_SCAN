def scan_tool
def target
pipeline{
    agent any
    parameters{
        choice choices:["nmap","sslyze","All"],
                 description: 'Choose as your requuired scan tool',
                 name: 'SCAN_TOOL'
        string defaultValue: "demo.testfire.net",
                 description: "please proveide the domain",
                 name: 'TARGET'
    
    }
    stages{
        stage('Pipeline Info'){
                steps{
                    script{
                        echo"<--Parameter Initialization-->"
                        echo"""
                        The current parameteres are:
                             Scan Tool: ${params.SCAN_TOOL}
                             Target: ${params.TARGET}
                        """
                    }
                }
        }
        stage("Scan the target domain"){
            steps{
                script{
                    scan_tool ="${params.SCAN_TOOL}"
                    echo"----> scan_tool:$scan_tool"
                    target = "${params.TARGET}"
                    if(scan_tool=="nmap"){
                          echo "nmap scan commited"
                        sh """
                        sudo nmap -v  --script http-slowloris-check $target -oN nmap_report.txt
                        """
                        echo "report name change"
                        sh 'sudo mv nmap_report.txt nmap_report_$(date +"%m_%d_%Y_%H:%M").txt'     
                    }
                    else if(scan_tool=="sslyze"){
                        echo "sslyze scan commited"
                        sh"""
                        sudo sslyze $target --certinfo -oN sslyze_report.txt 
                        """
                        echo "report name change"
                        sh 'sudo mv sslyze_report.txt sslyze_report_$(date +"%m_%d_%Y_%H:%M").txt'                        
                    }
                    else{
                        sh"""
                        sudo nmap $target -p22-443 > nmap_report.txt
                        sudo sslyze $taget --certinfo > sslyze_report.txt 
                        """
                        echo "report name change"
                        sh '''
                        sudo mv nmap_report.txt nmap_report_$(date +"%m_%d_%Y_%H:%M").txt
                        sudo mv sslyze_report.txt nmap_report_$(date +"%m_%d_%Y_%H:%M").txt
                        '''
                    }
                    }
                }
            }
        }
        post {
             always {
                 echo "Removing unkown files and complete the scan"
                 sh '''
                    sudo rm Jenkinsfile
                 '''
                
             }
        }

    }

    
    
    
    
