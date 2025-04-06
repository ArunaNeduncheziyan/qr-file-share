import java.util.Scanner;
class while
{
    public static void main(String args[])
    {
        System.out.print("Enter i value which is lessthen 10:");
        
        Scanner scan = new Scanner(System.in);
        int i = scan.nextInt();

        while(i<=10)
        {
            System.out.println("the value of i:");
            i++;
        }
    }
}