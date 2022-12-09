#include <stdio.h>

int main()
{
    int modulo;
    int dividend;
    int divisor;

    while (1)
    {
        printf("Enter dividend (or -99 to STOP): ");
        scanf("%d", &dividend);

        if (dividend == -99)
            break;

        printf("Enter divisor: ");
        scanf("%d", &divisor);

        modulo = dividend % divisor;

        printf("%d modulo %d is %d\n", dividend, divisor, modulo);
    }


    return 0;

}